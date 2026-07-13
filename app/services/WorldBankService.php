<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WorldBankService
{
    public function getCountryData($iso3)
    {
        $indicators = [
            'gdp'        => 'NY.GDP.MKTP.CD',
            'inflation'  => 'FP.CPI.TOTL.ZG',
            'population' => 'SP.POP.TOTL',
            'exports'    => 'NE.EXP.GNFS.CD',
            'imports'    => 'NE.IMP.GNFS.CD'
        ];

        $result = [];

        foreach ($indicators as $key => $indicator) {

            try {

    $response = Http::timeout(10)->get(
        "https://api.worldbank.org/v2/country/{$iso3}/indicator/{$indicator}?format=json&per_page=5"
    );

} catch (\Exception $e) {

    continue;

}

            if ($response->successful()) {

                $json = $response->json();

                if (isset($json[1])) {

                    foreach ($json[1] as $row) {

                        if (!is_null($row['value'])) {

                            $result[$key] = $row['value'];

                            $result[$key . '_year'] = $row['date'];

                            break;
                        }
                    }
                }
            }
        }

        return $result;
    }
}