<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;


Route::get('/country', [CountryController::class, 'index'])->name('country');
Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/country', [CountryController::class, 'index'])->name('country');
    Route::get('/country/{iso2}', [CountryController::class, 'show'])->name('country.show');


    Route::view('/weather', 'weather')->name('weather');
    Route::view('/currency', 'currency')->name('currency');
    Route::view('/news', 'news')->name('news');
    Route::view('/ports', 'ports')->name('ports');
    Route::view('/analytics', 'analytics')->name('analytics');
    Route::view('/compare', 'compare')->name('compare');
    Route::view('/watchlist', 'watchlist')->name('watchlist');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';