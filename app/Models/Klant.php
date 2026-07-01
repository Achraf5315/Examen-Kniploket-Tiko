<?php

namespace App\Models;

use Database\Factories\KlantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Klant extends TikoModel
{
    /** @use HasFactory<KlantFactory> */
    use HasFactory;

    protected $table = 'Klant';

    public function gebruiker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'GebruikerId', 'Id');
    }

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
