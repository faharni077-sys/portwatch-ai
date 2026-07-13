<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortController;
use App\Http\Controllers\DashboardController;





Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

    Route::get('/countries', [CountryController::class, 'index'])
    ->name('countries.index');
    Route::get('/country/{iso2}', [CountryController::class, 'show'])->name('country.show');


    Route::view('/weather', 'weather')->name('weather');
    Route::view('/currency', 'currency')->name('currency');
    Route::view('/news', 'news')->name('news');
    Route::get('/ports', [PortController::class, 'index'])->name('ports.index');
    Route::get('/api/ports', [PortController::class,'getPorts']);
    Route::view('/analytics', 'analytics')->name('analytics');
    Route::view('/compare', 'compare')->name('compare');
    Route::view('/watchlist', 'watchlist')->name('watchlist');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';