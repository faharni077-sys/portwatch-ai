<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Country;

/**
 * ProductionSeeder — safe to run on every Railway deploy.
 *
 * Design rules:
 *   1. Admin user and lexicon are seeded FIRST and never depend on
 *      external HTTP. They always succeed.
 *   2. Countries seed makes an HTTP call to GitHub. If it fails the
 *      method catches the exception and logs a warning — it does NOT
 *      re-throw, so the admin user seeding is never blocked.
 *   3. All writes use updateOrCreate / updateOrInsert — fully idempotent.
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Step 1: Admin user (no HTTP, always succeeds) ─────────────────
        // NOTE: Do NOT wrap password with Hash::make() here.
        // The User model has 'password' => 'hashed' in its casts array,
        // which automatically hashes the value on assignment.
        // Using Hash::make() + the hashed cast causes double-hashing,
        // which makes login fail even with the correct password.
        User::updateOrCreate(
            ['email' => 'admin@portwatch.ai'],
            [
                'name'     => 'Admin PortWatch',
                'password' => 'admin123456',
                'role'     => 'admin',
            ]
        );
        $this->command?->info('[1/3] Admin user: OK');

        // ── Step 2: Lexicon words (no HTTP, always succeeds) ──────────────
        $this->seedLexicon();
        $this->command?->info('[2/3] Lexicon: OK');

        // ── Step 3: Countries (HTTP to GitHub — best-effort, non-blocking) ─
        if (Country::count() === 0) {
            $this->seedCountries();
        } else {
            $this->command?->info('[3/3] Countries: already seeded (' . Country::count() . ' rows), skipping.');
        }

        $this->command?->info('ProductionSeeder: completed.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Countries — best-effort, never throws
    // ─────────────────────────────────────────────────────────────────────
    private function seedCountries(): void
    {
        try {
            $response = Http::timeout(20)->get(
                'https://raw.githubusercontent.com/mledoze/countries/master/countries.json'
            );

            if ($response->failed()) {
                $this->command?->warn('[3/3] Countries: GitHub returned non-2xx, skipping.');
                return;
            }

            $rows = $response->json();
            if (empty($rows)) {
                $this->command?->warn('[3/3] Countries: empty JSON from GitHub, skipping.');
                return;
            }

            foreach ($rows as $c) {
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

            $this->command?->info('[3/3] Countries: seeded ' . Country::count() . ' rows.');

        } catch (\Exception $e) {
            // Non-fatal — admin login still works even if countries seed fails.
            // DashboardController will auto-seed on first page load instead.
            $this->command?->warn('[3/3] Countries: exception (' . $e->getMessage() . ') — skipped. Will auto-seed on first dashboard load.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // Lexicon — pure data, no HTTP
    // ─────────────────────────────────────────────────────────────────────
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
    }
}
