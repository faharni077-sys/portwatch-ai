<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsService
{
    public function getNews($country)
    {
        $apiKey = env('GNEWS_API_KEY');

        $response = Http::get(
            'https://gnews.io/api/v4/search',
            [
                'q' => $country . ' logistics OR trade OR shipping OR economy',
                'lang' => 'en',
                'max' => 5,
                'apikey' => $apiKey
            ]
        );

        if ($response->successful()) {
            return $response->json()['articles'] ?? [];
        }

        return [];
    }
}