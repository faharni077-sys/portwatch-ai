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
use Carbon\Carbon;
use App\Services\SentimentService;


class CountryController extends Controller
{
    public function index()
    {
        $response = Http::get(
            'https://raw.githubusercontent.com/mledoze/countries/master/countries.json'
        );

        if ($response->failed()) {
            abort(500, 'Failed to fetch countries.');
        }

        $countries = collect($response->json())
            ->sortBy('name.common')
            ->values();

        return view('countries.index', compact('countries'));
    }

    public function show(
    $iso2,
    WeatherService $weatherService,
    WorldBankService $worldBankService,
    ExchangeRateService $exchangeRateService,
    NewsService $newsService,
    SentimentService $sentimentService
)
    {
    $response = Http::get(
        'https://raw.githubusercontent.com/mledoze/countries/master/countries.json'
    );

    $countries = collect($response->json());

    $country = $countries->first(function ($item) use ($iso2) {
        return strtoupper($item['cca2']) == strtoupper($iso2);
    });

    if (!$country) {
        abort(404);
    }

    $lat = $country['latlng'][0] ?? null;
    $lon = $country['latlng'][1] ?? null;

    $weather = null;

    if (!is_null($lat) && !is_null($lon)) {
    $weather = $weatherService->getWeather($lat, $lon);
}


$countryDb = Country::where('code', $country['cca2'])->first();

if ($countryDb && $weather) {

    WeatherLog::updateOrCreate(

        [
            'country_id' => $countryDb->id
        ],

        [
            'temperature' => $weather['current']['temperature_2m'] ?? null,
            'rainfall'    => $weather['current']['rain'] ?? 0,
            'wind_speed'  => $weather['current']['wind_speed_10m'] ?? null,

            'storm_risk'  =>
                ($weather['current']['wind_speed_10m'] ?? 0) > 30
                ? 'High'
                : 'Low'
        ]

    );

}

$worldBank = [];
$exchange = null;

if (isset($country['cca3'])) {
    $worldBank = $worldBankService->getCountryData($country['cca3']);
}

// ── P2: Cache World Bank data ke tabel economic_data ──────────────
if ($countryDb && !empty($worldBank)) {
    EconomicData::updateOrCreate(
        [
            'country_id' => $countryDb->id,
            'year'       => (int) ($worldBank['gdp_year'] ?? date('Y')),
        ],
        [
            'gdp'       => $worldBank['gdp']      ?? null,
            'inflation' => $worldBank['inflation'] ?? null,
            'export'    => isset($worldBank['exports']) ? (int) $worldBank['exports'] : null,
            'import'    => isset($worldBank['imports']) ? (int) $worldBank['imports'] : null,
        ]
    );
}

$currency = array_key_first($country['currencies']);

$exchange = $exchangeRateService->getRates($currency);
if ($countryDb && $exchange) {

    $targets = ['IDR', 'USD', 'EUR', 'JPY', 'CNY'];

foreach ($targets as $target) {

    if (isset($exchange['rates'][$target])) {

        CurrencyRate::updateOrCreate(

            [
                'country_id' => $countryDb->id,
                'target_currency' => $target
            ],

            [
                'base_currency' => $currency,
                'exchange_rate' => $exchange['rates'][$target]
            ]

        );

        

}

    }

    if ($countryDb) {

    // Weather Risk
    $positive = NewsCache::where('country_id', $countryDb->id)
    ->where('sentiment', 'Positive')
    ->count();

$neutral = NewsCache::where('country_id', $countryDb->id)
    ->where('sentiment', 'Neutral')
    ->count();

$negative = NewsCache::where('country_id', $countryDb->id)
    ->where('sentiment', 'Negative')
    ->count();

$totalNews = $positive + $neutral + $negative;

$newsRisk = 20;

if ($totalNews > 0) {

    $negativePercent = ($negative / $totalNews) * 100;

    if ($negativePercent >= 60) {
        $newsRisk = 80;
    } elseif ($negativePercent >= 30) {
        $newsRisk = 50;
    } else {
        $newsRisk = 20;
    }

}

    $weatherRisk = 20;

    if ($weather) {

        $wind = $weather['current']['wind_speed_10m'] ?? 0;

        if ($wind > 30) {
            $weatherRisk = 80;
        } elseif ($wind > 20) {
            $weatherRisk = 50;
        }

    }

    // Inflation Risk
    $inflationRisk = 30;

    if (isset($worldBank['inflation'])) {

        $inflation = (float) $worldBank['inflation'];

        if ($inflation > 10)
            $inflationRisk = 80;
        elseif ($inflation > 5)
            $inflationRisk = 50;
    }

    // Currency Risk
    $currencyRisk = 20;

    if (isset($exchange['rates']['USD'])) {

        $usd = $exchange['rates']['USD'];

        if ($usd < 0.7 || $usd > 1.3)
            $currencyRisk = 70;
    }


    $totalRisk = (
        $weatherRisk +
        $inflationRisk +
        $currencyRisk +
        $newsRisk
    ) / 4;

    $level = 'Low';

    if ($totalRisk >= 70)
        $level = 'High';
    elseif ($totalRisk >= 40)
        $level = 'Medium';

    $articles = $newsService->getNews($country['name']['common']);

foreach ($articles as $article) {

    NewsCache::updateOrCreate(

        [
            'url' => $article['url']
        ],

        [
            'country_id'   => $countryDb->id,
            'title'        => $article['title'],
            'description'  => $article['description'] ?? null,
            'source'       => $article['source']['name'] ?? '-',
            'published_at' => date(
    'Y-m-d H:i:s',
    strtotime($article['publishedAt'])
),
            'sentiment' => $sentimentService->analyze(
    $article['title'] . ' ' . ($article['description'] ?? '')
)
        ]

    );

}


    RiskScore::updateOrCreate(

        [
            'country_id' => $countryDb->id
        ],

        [
            'weather_risk' => $weatherRisk,
            'inflation_risk' => $inflationRisk,
            'currency_risk' => $currencyRisk,
            'news_risk' => $newsRisk,
            'total_risk' => $totalRisk,
            'risk_level' => $level
        ]

    );

    // ── P1: Simpan aggregate sentiment ke tabel sentiment_results ──
    if ($totalNews > 0) {
        // Tentukan overall sentiment dari nilai terbesar
        $overallSentiment = 'Neutral';
        if ($positive > $negative && $positive >= $neutral) {
            $overallSentiment = 'Positive';
        } elseif ($negative > $positive && $negative >= $neutral) {
            $overallSentiment = 'Negative';
        }

        SentimentResult::updateOrCreate(
            ['country_id' => $countryDb->id],
            [
                'positive'  => $positive,
                'neutral'   => $neutral,
                'negative'  => $negative,
                'sentiment' => $overallSentiment,
            ]
        );
    }
}

}
    return view('countries.show', compact(
    'country',
    'weather',
    'worldBank',
    'exchange'
));
    }
}