<?php

namespace App\Http\Controllers;

use App\Services\CountryService;
use App\Services\WorldBankService;
use Illuminate\Support\Facades\Http;

class CountryController extends Controller
{
    protected $countryService;
    protected $worldBankService;

    public function __construct(
        CountryService $countryService,
        WorldBankService $worldBankService
    ) {
        $this->countryService = $countryService;
        $this->worldBankService = $worldBankService;
    }

   public function index()
{
    $countries = $this->countryService->getCountries();

    return view('country', compact('countries'));
}

 public function show($iso2)
{
    $response = Http::get("https://restcountries.francecentral.azurecontainerapps.io/v3.1/alpha/{$iso2}");

    dd($response->status(), $response->body());
}
}