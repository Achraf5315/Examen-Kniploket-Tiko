<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model voor medewerkers van Kniploket Tiko.
 *
 * Koppelt een medewerker aan de bijbehorende gebruiker (login), adres,
 * behandelingen die zij mogen uitvoeren, werktijden en afspraken.
 */
class Medewerker extends TikoModel
{
    /** @use HasFactory<\Database\Factories\MedewerkerFactory> */
    use HasFactory;

    // De databasetabel gebruikt PascalCase-namen (afwijkend van de Laravel-conventie).
    protected $table = 'Medewerker';

    // Eén-op-één: elke medewerker hoort bij precies één gebruikersaccount.
    public function gebruiker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'GebruikerId', 'Id');
    }

    public function adres(): BelongsTo
    {
        return $this->belongsTo(Adres::class, 'AdresId', 'Id');
    }

    // Veel-op-veel: welke behandelingen deze medewerker mag uitvoeren.
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
