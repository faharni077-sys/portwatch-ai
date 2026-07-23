<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class NewsService
{
    public function getNews($country)
    {
        // Use config() not env() — env() returns null after config:cache runs on Railway
        $apiKey = config('services.gnews.key');

        if (empty($apiKey)) {
            return [];
        }

        $from = now()->subDays(7)->toIso8601ZuluString();

        try {
            $response = Http::timeout(10)->get(
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
        } catch (\Exception $e) {
            // API unreachable — return empty, caller handles gracefully
        }

        return [];
    }
}
