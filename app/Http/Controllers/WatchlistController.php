<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Watchlist;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    /**
     * GET /api/watchlist
     * Return all watchlist entries for the authenticated user.
     */
    public function index()
    {
        $items = Watchlist::where('user_id', auth()->id())
            ->with('country')
            ->latest()
            ->get()
            ->map(fn($w) => [
                'id'           => $w->id,
                'country_id'   => $w->country_id,
                // Use denormalized columns as primary source; fall back to relation
                'country_name' => $w->country_name ?? $w->country?->name ?? '—',
                'country_code' => $w->country_code ?? $w->country?->code ?? '—',
                'priority'     => $w->priority,
                'added_at'     => $w->created_at->toISOString(),
            ]);

        return response()->json($items);
    }

    /**
     * POST /api/watchlist
     * Add a country to the authenticated user's watchlist.
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_name' => 'required|string|max:100',
            'country_code' => 'required|string|max:10',
            'priority'     => 'in:HIGH,MEDIUM,LOW',
        ]);

        $code     = strtoupper(trim($request->country_code));
        $name     = trim($request->country_name);
        $priority = $request->priority ?? 'MEDIUM';

        // Prevent duplicates per user
        $exists = Watchlist::where('user_id', auth()->id())
            ->where('country_code', $code)
            ->exists();

        if ($exists) {
            return response()->json(['message' => "{$name} sudah ada di watchlist."], 409);
        }

        // Try to resolve country_id from the countries table (by code first, then by name)
        $country = Country::whereRaw('UPPER(code) = ?', [$code])->first()
                ?? Country::whereRaw('UPPER(name) = ?', [strtoupper($name)])->first();

        // country_id column is NOT NULL — require a valid match
        if (! $country) {
            return response()->json([
                'message' => "Negara '{$name}' ({$code}) tidak ditemukan dalam database. Gunakan kode ISO2 yang valid.",
            ], 422);
        }

        $wl = Watchlist::create([
            'user_id'      => auth()->id(),
            'country_id'   => $country->id,
            'priority'     => $priority,
            'country_name' => $country->name,
            'country_code' => strtoupper($country->code),
        ]);

        return response()->json([
            'id'           => $wl->id,
            'country_id'   => $wl->country_id,
            'country_name' => $wl->country_name,
            'country_code' => $wl->country_code,
            'priority'     => $wl->priority,
            'added_at'     => $wl->created_at->toISOString(),
        ], 201);
    }

    /**
     * DELETE /api/watchlist/{id}
     * Remove a watchlist entry (only if it belongs to the authenticated user).
     */
    public function destroy($id)
    {
        $wl = Watchlist::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $wl->delete();

        return response()->json(['message' => 'Dihapus dari watchlist.']);
    }

    /**
     * PATCH /api/watchlist/{id}/priority
     * Update the priority of a watchlist entry.
     */
    public function updatePriority(Request $request, $id)
    {
        $request->validate(['priority' => 'required|in:HIGH,MEDIUM,LOW']);

        $wl = Watchlist::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $wl->update(['priority' => $request->priority]);

        return response()->json(['message' => 'Priority diperbarui.']);
    }
}
