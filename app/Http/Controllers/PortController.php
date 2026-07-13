<?php

namespace App\Http\Controllers;

use App\Models\Port;
use App\Models\Country;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index()
{
    $ports = Port::with('country')
                ->limit(800)
                ->get();

    $countries = Country::orderBy('name')->get();

    return view(
        'ports.index',
        compact('ports','countries')
    );
}

    public function getPorts(Request $request)
{
    $query = Port::with('country');

    if ($request->country) {

        $query->whereHas('country', function ($q) use ($request) {

            $q->where('code', $request->country);

        });

    }

    if ($request->search) {

    $query->where(function ($q) use ($request) {

        $q->where('port_name', 'LIKE', '%' . $request->search . '%')
          ->orWhere('city', 'LIKE', '%' . $request->search . '%')
          ->orWhereHas('country', function ($country) use ($request) {

              $country->where('name', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('code', 'LIKE', '%' . $request->search . '%');

          });

    });

}

    return response()->json(

        $query->limit(500)->get()

    );
}
}