<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Port;
use App\Models\Country;
use Illuminate\Http\Request;

class PortAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Port::with('country');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('port_name', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $ports     = $query->orderBy('port_name')->paginate(20)->withQueryString();
        $countries = Country::orderBy('name')->get();

        return view('admin.ports.index', compact('ports', 'countries'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.ports.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'port_name'  => 'required|string|max:255',
            'city'       => 'nullable|string|max:255',
            'latitude'   => 'nullable|numeric|between:-90,90',
            'longitude'  => 'nullable|numeric|between:-180,180',
        ]);

        Port::create($data);

        return redirect()->route('admin.ports.index')
            ->with('success', 'Pelabuhan berhasil ditambahkan.');
    }

    public function edit(Port $port)
    {
        $countries = Country::orderBy('name')->get();
        return view('admin.ports.edit', compact('port', 'countries'));
    }

    public function update(Request $request, Port $port)
    {
        $data = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'port_name'  => 'required|string|max:255',
            'city'       => 'nullable|string|max:255',
            'latitude'   => 'nullable|numeric|between:-90,90',
            'longitude'  => 'nullable|numeric|between:-180,180',
        ]);

        $port->update($data);

        return redirect()->route('admin.ports.index')
            ->with('success', 'Pelabuhan berhasil diperbarui.');
    }

    public function destroy(Port $port)
    {
        $port->delete();

        return redirect()->route('admin.ports.index')
            ->with('success', 'Pelabuhan berhasil dihapus.');
    }
}
