<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WorldBankService
{
    /**
     * Fetch all 5 World Bank indicators in parallel using Laravel's Http::pool().
     * Sequential fetches (5 × 10s timeout) could take up to 50s and reliably
     * hit Railway's 30s HTTP request timeout. Parallel reduces worst-case to ~8s.
     */
    public function getCountryData($iso3)
    {
        $indicators = [
            'gdp'        => 'NY.GDP.MKTP.CD',
            'inflation'  => 'FP.CPI.TOTL.ZG',
            'population' => 'SP.POP.TOTL',
            'exports'    => 'NE.EXP.GNFS.CD',
            'imports'    => 'NE.IMP.GNFS.CD',
        ];

        // Build parallel requests — all fire at the same time
        try {
            $responses = Http::pool(function ($pool) use ($iso3, $indicators) {
                $requests = [];
                foreach ($indicators as $key => $code) {
                    $requests[$key] = $pool->as($key)
                        ->timeout(8)
                        ->get("https://api.worldbank.org/v2/country/{$iso3}/indicator/{$code}?format=json&per_page=5");
                }
                return $requests;
            });
        } catch (\Exception $e) {
            // Pool itself failed (network error, DNS, etc.) — return empty
            return [];
        }

        $result = [];

        foreach ($indicators as $key => $code) {
            try {
                $response = $responses[$key];

                if (!$response->successful()) {
                    continue;
                }

                $json = $response->json();

                if (!isset($json[1]) || !is_array($json[1])) {
                    continue;
                }

                foreach ($json[1] as $row) {
                    if (!is_null($row['value'] ?? null)) {
                        $result[$key]            = $row['value'];
                        $result[$key . '_year']  = $row['date'];
                        break;
                    }
                }
            } catch (\Exception $e) {
                // Single indicator failed — skip it, keep the rest
                continue;
            }
        }

        return $result;
    }
}
