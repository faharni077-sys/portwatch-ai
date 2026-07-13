<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    protected $fillable = [
        'country_id',
        'base_currency',
        'target_currency',
        'exchange_rate'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}