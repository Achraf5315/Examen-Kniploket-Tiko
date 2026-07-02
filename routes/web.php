<?php

use App\Http\Controllers\BehandelingController;
use App\Http\Controllers\AfspraakController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Homepagina van Kniploket Tiko (conform de wireframe)
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Resource routes voor volledig Behandeling CRUD-overzicht.
    Route::resource('behandelingen', BehandelingController::class)
        ->parameters(['behandelingen' => 'behandeling'])
        ->except(['show']);
});

// Afsprakenbeheer: alleen toegankelijk voor de rollen Admin (eigenaar/beheerder) en Medewerker
Route::middleware(['auth', 'rol:Admin,Medewerker'])->group(function () {
    // Overzicht van alle afspraken (Read)
    Route::get('/afspraken', [AfspraakController::class, 'index'])->name('afspraken.index');

    // Afspraak toevoegen (Create): formulier tonen en opslaan
    Route::get('/afspraken/toevoegen', [AfspraakController::class, 'create'])->name('afspraken.create');
    Route::post('/afspraken', [AfspraakController::class, 'store'])->name('afspraken.store');

    // Afspraak wijzigen (Update): formulier tonen en opslaan
    Route::get('/afspraken/{id}/wijzigen', [AfspraakController::class, 'edit'])->whereNumber('id')->name('afspraken.edit');
    Route::put('/afspraken/{id}', [AfspraakController::class, 'update'])->whereNumber('id')->name('afspraken.update');

    // Verwijderlink blijft bestaan en opent nu de bevestigingsmodal op het overzicht
    Route::get('/afspraken/{id}/verwijderen', [AfspraakController::class, 'delete'])->whereNumber('id')->name('afspraken.delete');

    Route::delete('/afspraken/{id}', [AfspraakController::class, 'destroy'])->whereNumber('id')->name('afspraken.destroy');
});

require __DIR__.'/auth.php';
