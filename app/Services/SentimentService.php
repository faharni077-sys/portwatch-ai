<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;

class SentimentService
{
    /**
     * Analyze text sentiment using the lexicon stored in the database.
     * Falls back to a built-in list if the database tables are empty.
     */
    public function analyze($text)
    {
        // Load from DB lexicon tables
        $positiveWords = PositiveWord::pluck('word')->toArray();
        $negativeWords = NegativeWord::pluck('word')->toArray();

        // Fallback if DB tables are empty
        if (empty($positiveWords)) {
            $positiveWords = [
                'growth', 'increase', 'success', 'profit', 'safe',
                'stable', 'recover', 'improve', 'peace', 'surge',
                'gain', 'boost', 'strong', 'rise', 'expand',
            ];
        }

        if (empty($negativeWords)) {
            $negativeWords = [
                'war', 'conflict', 'crisis', 'attack', 'risk',
                'inflation', 'terror', 'disaster', 'decline', 'sanction',
                'shortage', 'delay', 'strike', 'protest', 'collapse',
            ];
        }

        $text  = strtolower($text);
        $score = 0;

        foreach ($positiveWords as $word) {
            if (str_contains($text, $word)) {
                $score++;
            }
        }

        foreach ($negativeWords as $word) {
            if (str_contains($text, $word)) {
                $score--;
            }
        }

        if ($score > 0) return 'Positive';
        if ($score < 0) return 'Negative';

        return 'Neutral';
    }
}
