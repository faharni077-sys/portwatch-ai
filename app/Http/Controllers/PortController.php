<?php

namespace App\Http\Controllers;

use App\Models\Port;
use App\Models\Country;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index()
    {
        // Pass only the country list to the view.
        // The actual ports are loaded via AJAX from getPorts().
        $ports     = collect(); // empty — view uses AJAX
        $countries = Country::orderBy('name')->get();

        return view('ports.index', compact('ports', 'countries'));
    }

    public function getPorts(Request $request)
    {
        $query = Port::with('country');

        if ($request->filled('country')) {
            $query->whereHas('country', function ($q) use ($request) {
                $q->where('code', $request->country);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('port_name', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhereHas('country', function ($c) use ($search) {
                      $c->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Cap at 2000 so Leaflet stays responsive while showing more ports.
        // The /ports page sidebar list is still capped at 100 in the blade.
        return response()->json(
            $query->limit(2000)->get()
        );
    }
}
