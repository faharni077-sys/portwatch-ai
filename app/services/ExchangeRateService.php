<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    public function getRates($base = 'USD')
    {
        try {
            $response = Http::timeout(10)->get(
                "https://open.er-api.com/v6/latest/{$base}"
            );

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // API unreachable on Railway — return null, caller handles gracefully
        }

        return null;
    }
}
