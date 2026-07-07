<?php

namespace App\Models;

use Database\Factories\KlantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model voor klanten van Kniploket Tiko.
 *
 * Koppelt een klant aan de bijbehorende gebruiker (login), adressen,
 * afspraken en bestellingen.
 */
class Klant extends TikoModel
{
    /** @use HasFactory<KlantFactory> */
    use HasFactory;

    // De databasetabel gebruikt PascalCase-namen (afwijkend van de Laravel-conventie).
    protected $table = 'Klant';

    // Eén-op-één: elke klant hoort bij precies één gebruikersaccount.
    public function gebruiker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'GebruikerId', 'Id');
    }

    // Eén klant kan meerdere adressen hebben (bijv. thuis- en factuuradres).
    public function adressen(): HasMany
    {
        return $this->hasMany(Adres::class, 'KlantId', 'Id');
    }

    public function afspraken(): HasMany
    {
        return $this->hasMany(Afspraak::class, 'KlantId', 'Id');
    }

    public function bestellingen(): HasMany
    {
        return $this->hasMany(Bestelling::class, 'KlantId', 'Id');
    }
}
