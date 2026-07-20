<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Watchlist;
use App\Models\User;
use Illuminate\Http\Request;

class WatchlistAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Watchlist::with(['user', 'country']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('user', function ($q2) use ($request) {
                    $q2->where('name', 'like', '%' . $request->search . '%');
                })
                ->orWhere('country_name', 'like', '%' . $request->search . '%')
                ->orWhere('country_code', 'like', '%' . $request->search . '%');
            });
        }

        $watchlists = $query->latest()->paginate(20)->withQueryString();
        $users      = User::where('role', 'user')->orderBy('name')->get();

        // Stats
        $allWatchlists = Watchlist::when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id));
        $stats = [
            'high'   => (clone $allWatchlists)->where('priority', 'HIGH')->count(),
            'medium' => (clone $allWatchlists)->where('priority', 'MEDIUM')->count(),
            'low'    => (clone $allWatchlists)->where('priority', 'LOW')->count(),
        ];

        return view('admin.watchlists.index', compact('watchlists', 'users', 'stats'));
    }
}
