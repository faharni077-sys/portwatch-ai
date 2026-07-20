<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsService
{
    public function getNews($country)
    {
        $apiKey = env('GNEWS_API_KEY');

        // Only fetch articles published in the last 7 days, newest first
        $from = now()->subDays(7)->toIso8601ZuluString();

        $response = Http::get(
            'https://gnews.io/api/v4/search',
            [
                'q'      => $country . ' logistics OR trade OR shipping OR economy',
                'lang'   => 'en',
                'max'    => 5,
                'sortby' => 'publishedAt',
                'from'   => $from,
                'apikey' => $apiKey,
            ]
        );

        if ($response->successful()) {
            return $response->json()['articles'] ?? [];
        }

        return [];
    }
}
