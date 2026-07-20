<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Country;

/**
 * ProductionSeeder — safe to run on every Railway deploy.
 *
 * All operations use updateOrCreate / insertOrIgnore so re-running
 * this seeder never duplicates or overwrites user data.
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Admin user ────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@portwatch.ai'],
            [
                'name'     => 'Admin PortWatch',
                'password' => Hash::make('admin123456'),
                'role'     => 'admin',
            ]
        );

        $this->command?->info('Admin user: OK');

        // ── 2. Countries — only seed if table is empty ───────────────────
        if (Country::count() === 0) {
            $this->seedCountries();
        } else {
            $this->command?->info('Countries: already seeded (' . Country::count() . ' rows), skipping.');
        }

        // ── 3. Lexicon words (positive / negative) ───────────────────────
        $this->seedLexicon();

        $this->command?->info('ProductionSeeder: completed successfully.');
    }

    // ── Countries ────────────────────────────────────────────────────────
    private function seedCountries(): void
    {
        try {
            $response = Http::timeout(15)->get(
                'https://raw.githubusercontent.com/mledoze/countries/master/countries.json'
            );

            if ($response->failed()) {
                $this->command?->warn('Countries: failed to fetch from GitHub, skipping.');
                return;
            }

            foreach ($response->json() as $c) {
                Country::updateOrCreate(
                    ['code' => $c['cca2']],
                    [
                        'name'       => $c['name']['common'] ?? '-',
                        'capital'    => $c['capital'][0]     ?? '-',
                        'currency'   => isset($c['currencies'])
                            ? implode(',', array_keys($c['currencies'])) : '-',
                        'region'     => $c['region']   ?? '-',
                        'language'   => isset($c['languages'])
                            ? implode(',', $c['languages']) : '-',
                        'population' => 0,
                        'flag'       => "https://flagsapi.com/{$c['cca2']}/flat/64.png",
                    ]
                );
            }

            $this->command?->info('Countries: seeded ' . Country::count() . ' rows.');
        } catch (\Exception $e) {
            $this->command?->warn('Countries: exception — ' . $e->getMessage());
        }
    }

    // ── Lexicon ──────────────────────────────────────────────────────────
    private function seedLexicon(): void
    {
        $positive = [
            'growth','increase','success','profit','safe','stable','recover',
            'improve','peace','surge','gain','boost','strong','rise','expand',
            'agreement','deal','partner','positive','record','advance','progress',
            'invest','opportunity','develop','thrive','surplus','export','prosper',
            'efficient','reliable','open','cooperation','favorable','demand',
        ];

        $negative = [
            'war','conflict','crisis','attack','risk','inflation','terror',
            'disaster','decline','sanction','shortage','delay','strike','protest',
            'collapse','ban','tariff','drop','fall','loss','flood','storm',
            'disruption','recession','debt','default','freeze','blockade',
            'embargo','piracy','congestion','accident','failure','outage',
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

        $this->command?->info('Lexicon: seeded ' . count($positive) . ' positive + ' . count($negative) . ' negative words.');
    }
}
