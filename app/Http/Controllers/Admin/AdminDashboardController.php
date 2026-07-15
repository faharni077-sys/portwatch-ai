<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Port;
use App\Models\Article;
use App\Models\Watchlist;
use App\Models\Country;
use App\Models\NewsCache;
use App\Models\RiskScore;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'      => User::count(),
            'total_admins'     => User::where('role', 'admin')->count(),
            'total_countries'  => Country::count(),
            'total_ports'      => Port::count(),
            'total_articles'   => Article::count(),
            'total_watchlists' => Watchlist::count(),
            'total_news_cache' => NewsCache::count(),
            'total_risk_scores'=> RiskScore::count(),
        ];

        $recent_users    = User::latest()->take(5)->get();
        $recent_articles = Article::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_users', 'recent_articles'));
    }
}
