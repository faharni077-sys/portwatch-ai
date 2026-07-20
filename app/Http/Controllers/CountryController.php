<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Services\WorldBankService;
use App\Services\WeatherService;
use App\Services\ExchangeRateService;
use App\Models\Country;
use App\Models\CurrencyRate;
use App\Models\WeatherLog;
use App\Models\RiskScore;
use App\Models\EconomicData;
use App\Models\SentimentResult;
use App\Services\NewsService;
use App\Models\NewsCache;
use App\Services\SentimentService;

class CountryController extends Controller
{
    public function index()
    {
        try {
            $response = Http::timeout(10)->get(
                'https://raw.githubusercontent.com/mledoze/countries/master/countries.json'
            );

            if ($response->failed()) {
                abort(500, 'Failed to fetch countries.');
            }

            $countries = collect($response->json())
                ->sortBy('name.common')
                ->values();
        } catch (\Exception $e) {
            abort(500, 'Failed to fetch countries: ' . $e->getMessage());
        }

        return view('countries.index', compact('countries'));
    }

    public function show(
        $iso2,
        WeatherService $weatherService,
        WorldBankService $worldBankService,
        ExchangeRateService $exchangeRateService,
        NewsService $newsService,
        SentimentService $sentimentService
    ) {
        // ── Fetch country list from mledoze (with fallback) ───────────────
        try {
            $response = Http::timeout(10)->get(
                'https://raw.githubusercontent.com/mledoze/countries/master/countries.json'
            );
            $countries = collect($response->successful() ? $response->json() : []);
        } catch (\Exception $e) {
            $countries = collect([]);
        }

        $country = $countries->first(function ($item) use ($iso2) {
            return strtoupper($item['cca2'] ?? '') === strtoupper($iso2);
        });

        if (!$country) {
            abort(404);
        }

        // ── Defaults so the view never receives undefined variables ───────
        $weather   = null;
        $worldBank = [];
        $exchange  = null;

        $lat = $country['latlng'][0] ?? null;
        $lon = $country['latlng'][1] ?? null;

        // ── Weather ───────────────────────────────────────────────────────
        if (!is_null($lat) && !is_null($lon)) {
            try {
                $weather = $weatherService->getWeather($lat, $lon);
            } catch (\Exception $e) {
                $weather = null;
            }
        }

        // ── DB country record ─────────────────────────────────────────────
        $countryDb = Country::where('code', strtoupper($iso2))->first();

        // Persist weather log
        if ($countryDb && $weather) {
            try {
                WeatherLog::updateOrCreate(
                    ['country_id' => $countryDb->id],
                    [
                        'temperature' => $weather['current']['temperature_2m'] ?? null,
                        'rainfall'    => $weather['current']['rain'] ?? 0,
                        'wind_speed'  => $weather['current']['wind_speed_10m'] ?? null,
                        'storm_risk'  => ($weather['current']['wind_speed_10m'] ?? 0) > 30 ? 'High' : 'Low',
                    ]
                );
            } catch (\Exception $e) { /* non-fatal */ }
        }

        // ── World Bank ────────────────────────────────────────────────────
        if (!empty($country['cca3'])) {
            try {
                $worldBank = $worldBankService->getCountryData($country['cca3']);
            } catch (\Exception $e) {
                $worldBank = [];
            }
        }

        // Persist economic data
        if ($countryDb && !empty($worldBank)) {
            try {
                EconomicData::updateOrCreate(
                    [
                        'country_id' => $countryDb->id,
                        'year'       => (int) ($worldBank['gdp_year'] ?? date('Y')),
                    ],
                    [
                        'gdp'       => $worldBank['gdp']     ?? null,
                        'inflation' => $worldBank['inflation'] ?? null,
                        'export'    => isset($worldBank['exports'])  ? (int) $worldBank['exports']  : null,
                        'import'    => isset($worldBank['imports'])  ? (int) $worldBank['imports']  : null,
                    ]
                );
            } catch (\Exception $e) { /* non-fatal */ }
        }

        // ── Exchange Rate ─────────────────────────────────────────────────
        $currency = !empty($country['currencies'])
            ? array_key_first($country['currencies'])
            : 'USD';

        try {
            $exchange = $exchangeRateService->getRates($currency);
        } catch (\Exception $e) {
            $exchange = null;
        }

        // Persist currency rates
        if ($countryDb && $exchange) {
            try {
                foreach (['IDR', 'USD', 'EUR', 'JPY', 'CNY'] as $target) {
                    if (isset($exchange['rates'][$target])) {
                        CurrencyRate::updateOrCreate(
                            ['country_id' => $countryDb->id, 'target_currency' => $target],
                            ['base_currency' => $currency, 'exchange_rate' => $exchange['rates'][$target]]
                        );
                    }
                }
            } catch (\Exception $e) { /* non-fatal */ }
        }

        // ── Risk calculation ──────────────────────────────────────────────
        if ($countryDb) {
            try {
                $positive  = NewsCache::where('country_id', $countryDb->id)->where('sentiment', 'Positive')->count();
                $neutral   = NewsCache::where('country_id', $countryDb->id)->where('sentiment', 'Neutral')->count();
                $negative  = NewsCache::where('country_id', $countryDb->id)->where('sentiment', 'Negative')->count();
                $totalNews = $positive + $neutral + $negative;
            } catch (\Exception $e) {
                $positive = $neutral = $negative = $totalNews = 0;
            }

            $newsRisk = 20;
            if ($totalNews > 0) {
                $negPct = ($negative / $totalNews) * 100;
                if ($negPct >= 60)      $newsRisk = 80;
                elseif ($negPct >= 30)  $newsRisk = 50;
            }

            $weatherRisk = 20;
            if ($weather) {
                $wind = $weather['current']['wind_speed_10m'] ?? 0;
                if ($wind > 30)      $weatherRisk = 80;
                elseif ($wind > 20)  $weatherRisk = 50;
            }

            $inflationRisk = 30;
            if (isset($worldBank['inflation'])) {
                $inf = (float) $worldBank['inflation'];
                if ($inf > 10)     $inflationRisk = 80;
                elseif ($inf > 5)  $inflationRisk = 50;
            }

            $currencyRisk = 20;
            if (isset($exchange['rates']['USD'])) {
                $usd = $exchange['rates']['USD'];
                if ($usd < 0.7 || $usd > 1.3) $currencyRisk = 70;
            }

            $totalRisk = ($weatherRisk + $inflationRisk + $currencyRisk + $newsRisk) / 4;
            $level = 'Low';
            if ($totalRisk >= 70)     $level = 'High';
            elseif ($totalRisk >= 40) $level = 'Medium';

            // Fetch and persist news
            try {
                $articles = $newsService->getNews($country['name']['common']);
                foreach ($articles as $article) {
                    NewsCache::updateOrCreate(
                        ['url' => $article['url']],
                        [
                            'country_id'   => $countryDb->id,
                            'title'        => $article['title'],
                            'description'  => $article['description'] ?? null,
                            'source'       => $article['source']['name'] ?? '-',
                            'published_at' => date('Y-m-d H:i:s', strtotime($article['publishedAt'])),
                            'sentiment'    => $sentimentService->analyze(
                                $article['title'] . ' ' . ($article['description'] ?? '')
                            ),
                        ]
                    );
                }
            } catch (\Exception $e) { /* non-fatal — news is optional */ }

            // Persist risk score
            try {
                RiskScore::updateOrCreate(
                    ['country_id' => $countryDb->id],
                    [
                        'weather_risk'   => $weatherRisk,
                        'inflation_risk' => $inflationRisk,
                        'currency_risk'  => $currencyRisk,
                        'news_risk'      => $newsRisk,
                        'total_risk'     => $totalRisk,
                        'risk_level'     => $level,
                    ]
                );
            } catch (\Exception $e) { /* non-fatal */ }

            // Persist sentiment aggregate
            if ($totalNews > 0) {
                try {
                    $overallSentiment = 'Neutral';
                    if ($positive > $negative && $positive >= $neutral)       $overallSentiment = 'Positive';
                    elseif ($negative > $positive && $negative >= $neutral)   $overallSentiment = 'Negative';

                    SentimentResult::updateOrCreate(
                        ['country_id' => $countryDb->id],
                        [
                            'positive'  => $positive,
                            'neutral'   => $neutral,
                            'negative'  => $negative,
                            'sentiment' => $overallSentiment,
                        ]
                    );
                } catch (\Exception $e) { /* non-fatal */ }
            }
        }

        return view('countries.show', compact('country', 'weather', 'worldBank', 'exchange'));
    }
}
