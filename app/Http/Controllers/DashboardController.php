<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name')->get();

        // If the countries table is empty (fresh Railway deploy before seeder runs),
        // trigger the seeder automatically so the dropdown is not blank.
        if ($countries->isEmpty()) {
            try {
                Artisan::call('db:seed', ['--class' => 'CountrySeeder', '--force' => true]);
                $countries = Country::orderBy('name')->get();
            } catch (\Exception $e) {
                // Seeder failed (e.g. external API down) — pass empty collection,
                // view handles the empty state gracefully.
            }
        }

        return view('dashboard.index', compact('countries'));
    }
}
