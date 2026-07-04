<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WorldBankService
{
    public function getPopulation($iso3)
    {
        $response = Http::get(
            "https://api.worldbank.org/v2/country/{$iso3}/indicator/SP.POP.TOTL?format=json"
        );

        if (!$response->successful()) {
            return 0;
        }

        $data = $response->json();

        if (isset($data[1])) {
            foreach ($data[1] as $item) {
                if (!empty($item['value'])) {
                    return $item['value'];
                }
            }
        }

        return 0;
    }
}