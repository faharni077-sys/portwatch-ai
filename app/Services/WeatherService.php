<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function getWeather($lat, $lon)
    {
        try {

            $response = Http::timeout(10)->get(
                'https://api.open-meteo.com/v1/forecast',
                [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'current' => 'temperature_2m,wind_speed_10m',
                ]
            );

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {

            return null;

        }
    }
}