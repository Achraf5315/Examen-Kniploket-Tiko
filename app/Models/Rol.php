<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model voor gebruikersrollen (bijv. Admin, Medewerker, Klant).
 *
 * De koppeling met gebruikers loopt via de pivot-tabel RolPerGebruiker.
 */
class Rol extends TikoModel
{
    /** @use HasFactory<\Database\Factories\RolFactory> */
    use HasFactory;

    // De databasetabel gebruikt PascalCase-namen (afwijkend van de Laravel-conventie).
    protected $table = 'Rol';

    public function gebruikers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'RolPerGebruiker', 'RolId', 'GebruikerId')
            ->withPivot(['Id', 'IsActief', 'Opmerking', 'DatumAangemaakt', 'DatumGewijzigd']);
    }

    public function rolPerGebruikers(): HasMany
    {
        return $this->hasMany(RolPerGebruiker::class, 'RolId', 'Id');
    }
}
