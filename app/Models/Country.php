<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'capital',
        'currency',
        'region',
        'language',
        'population',
        'flag'
    ];

    public function weatherLogs()
{
    return $this->hasMany(WeatherLog::class);
}

public function currencyRates()
{
    return $this->hasMany(CurrencyRate::class);
}

public function riskScores()
{
    return $this->hasMany(RiskScore::class);
}

public function news()
{
    return $this->hasMany(NewsCache::class);
}

}