<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Country;

class SyncCountries extends Command
{
    protected $signature = 'country:sync';
    protected $description = 'Sync Countries from API';

    public function handle()
    {
        $response = Http::get('https://restcountries.francocarballar.com/api/v1/all');

        if (!$response->successful()) {
            $this->error('API gagal.');
            return;
        }

        $countries = $response->json();

        Country::truncate();

        foreach ($countries as $country) {

            Country::create([
                'code'       => $country['cca2'] ?? '',
                'name'       => $country['name']['common'] ?? '',
                'capital'    => $country['capital'][0] ?? '',
                'region'     => $country['region'] ?? '',
                'currency'   => isset($country['currencies'])
                    ? implode(', ', array_keys($country['currencies']))
                    : '',
                'language'   => isset($country['languages'])
                    ? implode(', ', $country['languages'])
                    : '',
                'flag'       => $country['flags']['png'] ?? '',
                'population' => $country['population'] ?? 0,
            ]);
        }

        $this->info('Countries berhasil disimpan.');
    }
}