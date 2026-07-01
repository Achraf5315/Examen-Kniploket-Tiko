<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medewerker extends TikoModel
{
    /** @use HasFactory<\Database\Factories\MedewerkerFactory> */
    use HasFactory;

    protected $table = 'Medewerker';

    public function gebruiker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'GebruikerId', 'Id');
    }

    public function adres(): BelongsTo
    {
        return $this->belongsTo(Adres::class, 'AdresId', 'Id');
    }

    public function behandelingen(): BelongsToMany
    {
        return $this->belongsToMany(Behandeling::class, 'MedewerkerPerBehandeling', 'MedewerkerId', 'BehandelingId')
            ->withPivot(['Id', 'IsActief', 'Opmerking', 'DatumAangemaakt', 'DatumGewijzigd']);
    }

    public function werktijden(): HasMany
    {
        return $this->hasMany(Werktijd::class, 'MedewerkerId', 'Id');
    }

    public function afspraken(): HasMany
    {
        return $this->hasMany(Afspraak::class, 'MedewerkerId', 'Id');
    }
}
