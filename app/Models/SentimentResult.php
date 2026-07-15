<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentimentResult extends Model
{
    protected $fillable = [
        'country_id',
        'positive',
        'neutral',
        'negative',
        'sentiment',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
