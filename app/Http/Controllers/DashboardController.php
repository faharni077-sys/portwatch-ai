<?php

namespace App\Http\Controllers;

use App\Models\Country;

class DashboardController extends Controller
{
    public function index()
{
    $countries = Country::orderBy('name')->get();

    return view('dashboard.index', compact('countries'));
}
}