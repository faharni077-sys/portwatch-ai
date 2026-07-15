<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Admin account ──────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@portwatch.ai'],
            [
                'name'     => 'Admin PortWatch',
                'password' => Hash::make('admin123456'),
                'role'     => 'admin',
            ]
        );

        // ── Default test user (role: user) ─────────────────────────
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'     => 'Test User',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]
        );

        // ── Existing seeders ────────────────────────────────────────
        $this->call([
            CountrySeeder::class,
            LexiconSeeder::class,
        ]);
    }
}
