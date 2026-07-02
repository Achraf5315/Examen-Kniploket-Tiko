<?php

use App\Http\Controllers\BehandelingController;
use App\Http\Controllers\AfspraakController;
use App\Http\Controllers\BestellingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

// Homepagina van Kniploket Tiko (conform de wireframe)
Route::get('/', function () {
    return view('home');
})->name('home');

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


Route::middleware(['auth'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});
require __DIR__.'/auth.php';
