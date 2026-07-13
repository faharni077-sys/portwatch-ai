<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LexiconSeeder extends Seeder
{
    public function run(): void
    {
        $positive = [
            'growth', 'increase', 'success', 'profit', 'safe', 'stable',
            'recover', 'improve', 'peace', 'surge', 'gain', 'boost', 'strong',
            'rise', 'expand', 'agreement', 'deal', 'partner', 'positive',
            'record', 'advance', 'progress', 'invest', 'opportunity', 'develop',
            'thrive', 'surplus', 'export', 'prosper', 'efficient', 'reliable',
            'open', 'cooperation', 'favorable', 'demand',
        ];

        $negative = [
            'war', 'conflict', 'crisis', 'attack', 'risk', 'inflation',
            'terror', 'disaster', 'decline', 'sanction', 'shortage', 'delay',
            'strike', 'protest', 'collapse', 'ban', 'tariff', 'drop', 'fall',
            'loss', 'flood', 'storm', 'disruption', 'recession', 'debt',
            'default', 'freeze', 'blockade', 'embargo', 'piracy', 'congestion',
            'accident', 'failure', 'outage',
        ];

        $now = now();

        foreach ($positive as $word) {
            DB::table('positive_words')->updateOrInsert(
                ['word' => $word],
                ['word' => $word, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        foreach ($negative as $word) {
            DB::table('negative_words')->updateOrInsert(
                ['word' => $word],
                ['word' => $word, 'created_at' => $now, 'updated_at' => $now]
            );
        }

        $this->command->info('Lexicon seeded: ' . count($positive) . ' positive, ' . count($negative) . ' negative words.');
    }
}
