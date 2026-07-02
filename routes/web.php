<?php

use App\Http\Controllers\AfspraakController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Afsprakenbeheer: alleen toegankelijk voor de rollen Admin (eigenaar/beheerder) en Medewerker
Route::middleware(['auth', 'rol:Admin,Medewerker'])->group(function () {
    // Overzicht van alle afspraken (Read)
    Route::get('/afspraken', [AfspraakController::class, 'index'])->name('afspraken.index');
});

require __DIR__.'/auth.php';
