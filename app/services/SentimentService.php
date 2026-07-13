<?php

namespace App\Services;

class SentimentService
{
    public function analyze($text)
    {
        $positive = [
            'growth','increase','success','profit','safe',
            'stable','recover','improve','peace'
        ];

        $negative = [
            'war','conflict','crisis','attack','risk',
            'inflation','terror','disaster','decline','sanction'
        ];

        $text = strtolower($text);

        $score = 0;

        foreach ($positive as $word) {
            if (str_contains($text, $word)) $score++;
        }

        foreach ($negative as $word) {
            if (str_contains($text, $word)) $score--;
        }

        if ($score > 0) return 'Positive';
        if ($score < 0) return 'Negative';

        return 'Neutral';
    }
}