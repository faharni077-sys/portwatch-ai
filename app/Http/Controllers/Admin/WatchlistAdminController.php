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
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('country', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $watchlists = $query->latest()->paginate(20)->withQueryString();
        $users      = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.watchlists.index', compact('watchlists', 'users'));
    }
}
