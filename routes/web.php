<?php

use App\Http\Controllers\BestellingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/bestellingen', [BestellingController::class, 'index'])
->name('bestellingen.index') 
->middleware(['auth', 'verified']);

Route::get('/bestellingen/create', [BestellingController::class, 'create'])
->name('bestellingen.create')
->middleware(['auth', 'verified']);

Route::post('/bestellingen', [BestellingController::class, 'store'])
->name('bestellingen.store')
->middleware(['auth', 'verified']);

Route::get('/bestellingen/{bestelling}/edit', [BestellingController::class, 'edit'])
->name('bestellingen.edit')
->middleware(['auth', 'verified']);

Route::post('/bestellingen/{bestelling}', [BestellingController::class, 'update'])
->name('bestellingen.update')
->middleware(['auth', 'verified']);

Route::delete('/bestellingen/{bestelling}', [BestellingController::class, 'destroy'])
->name('bestellingen.destroy')
->middleware(['auth', 'verified']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
