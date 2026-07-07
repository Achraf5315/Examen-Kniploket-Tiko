<?php

namespace App\Models;

use Database\Factories\RolPerGebruikerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Koppeltabel-model tussen Rol en User (Gebruiker): welke rol(len) een gebruiker heeft.
 */
class RolPerGebruiker extends TikoModel
{
    /** @use HasFactory<RolPerGebruikerFactory> */
    use HasFactory;

    // De databasetabel gebruikt PascalCase-namen (afwijkend van de Laravel-conventie).
    protected $table = 'RolPerGebruiker';

    public function gebruiker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'GebruikerId', 'Id');
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'RolId', 'Id');
    }
}
