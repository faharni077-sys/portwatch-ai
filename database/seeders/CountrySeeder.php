<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $response = Http::get(
            'https://raw.githubusercontent.com/mledoze/countries/master/countries.json'
        );

        if ($response->failed()) {
            $this->command->error('Failed to fetch countries.');
            return;
        }

        $countries = $response->json();

        foreach ($countries as $country) {

            Country::updateOrCreate(

                [
                    'code' => $country['cca2']
                ],

                [
                    'name' => $country['name']['common'] ?? '-',
                    'capital' => $country['capital'][0] ?? '-',
                    'currency' => isset($country['currencies'])
                        ? implode(',', array_keys($country['currencies']))
                        : '-',
                    'region' => $country['region'] ?? '-',
                    'language' => isset($country['languages'])
                        ? implode(',', $country['languages'])
                        : '-',
                    'population' => 0,
                    'flag' => "https://flagsapi.com/{$country['cca2']}/flat/64.png",
                ]

            );
        }

        $this->command->info('Countries imported successfully!');
    }
}