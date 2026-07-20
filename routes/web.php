<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WatchlistController;





Route::get('/', function () {
    return view('welcome');
});

// /api/ports is outside the auth middleware group so Leaflet AJAX calls
// work reliably on Railway HTTPS without session cookie issues.
// The endpoint itself checks auth via the middleware chain below.
Route::get('/api/ports', [PortController::class, 'getPorts'])->middleware('auth');

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

    Route::get('/countries', [CountryController::class, 'index'])
    ->name('countries.index');
    Route::get('/country/{iso2}', [CountryController::class, 'show'])->name('country.show');


    Route::view('/weather', 'weather')->name('weather');
    Route::view('/currency', 'currency')->name('currency');
    Route::get('/news', function () {
        $adminArticles = \App\Models\Article::latest()->get();
        return view('news', compact('adminArticles'));
    })->name('news');
    Route::get('/ports', [PortController::class, 'index'])->name('ports.index');
    // /api/ports is defined OUTSIDE this middleware group (see above) to avoid
    // Railway HTTPS session issues with AJAX — do not re-add it here.
    Route::view('/analytics', 'analytics')->name('analytics');
    Route::view('/compare', 'compare')->name('compare');
    Route::view('/watchlist', 'watchlist')->name('watchlist');

    // Watchlist API — CRUD endpoints (database-backed)
    Route::get('/api/watchlist',                    [WatchlistController::class, 'index']);
    Route::post('/api/watchlist',                   [WatchlistController::class, 'store']);
    Route::delete('/api/watchlist/{id}',            [WatchlistController::class, 'destroy']);
    Route::patch('/api/watchlist/{id}/priority',    [WatchlistController::class, 'updatePriority']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

// ============================================================
// ADMIN ROUTES — protected by auth + admin middleware
// ============================================================
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PortAdminController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\WatchlistAdminController;

Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    // Kelola User
    Route::get('/users',              [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create',       [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users',             [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit',  [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}',       [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}',    [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Kelola Dataset Pelabuhan
    Route::get('/ports',              [PortAdminController::class, 'index'])->name('ports.index');
    Route::get('/ports/create',       [PortAdminController::class, 'create'])->name('ports.create');
    Route::post('/ports',             [PortAdminController::class, 'store'])->name('ports.store');
    Route::get('/ports/{port}/edit',  [PortAdminController::class, 'edit'])->name('ports.edit');
    Route::put('/ports/{port}',       [PortAdminController::class, 'update'])->name('ports.update');
    Route::delete('/ports/{port}',    [PortAdminController::class, 'destroy'])->name('ports.destroy');

    // Kelola Artikel Analisis
    Route::get('/articles',                 [AdminArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create',          [AdminArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles',                [AdminArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}/edit',  [AdminArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}',       [AdminArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}',    [AdminArticleController::class, 'destroy'])->name('articles.destroy');

    // Lihat Watchlist Pengguna
    Route::get('/watchlists', [WatchlistAdminController::class, 'index'])->name('watchlists.index');
});

require __DIR__.'/auth.php';