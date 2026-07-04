<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CountryService
{
    public function getCountries()
    {
        $response = Http::withHeaders([
            'X-CSCAPI-KEY' => env('COUNTRY_API_KEY'),
        ])->get('https://api.countrystatecity.in/v1/countries');

        if (!$response->successful()) {
            dd($response->body());
        }

        return collect($response->json())->map(function ($country) {

    return [
    'name' => $country['name'],
    'flag' => "https://flagsapi.com/{$country['iso2']}/flat/64.png",
    'capital' => $country['capital'],
    'region' => $country['region'],
    'currency' => $country['currency'],
    'language' => '-',
    'population' => 0,
    'iso2' => $country['iso2'],
    'iso3' => $country['iso3'],
];

})->toArray();
    }
}