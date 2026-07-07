<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model voor een adres, gekoppeld aan één klant.
 */
class Adres extends TikoModel
{
    /** @use HasFactory<\Database\Factories\AdresFactory> */
    use HasFactory;

    // De databasetabel gebruikt PascalCase-namen (afwijkend van de Laravel-conventie).
    protected $table = 'Adres';

    public function klant(): BelongsTo
    {
        return $this->belongsTo(Klant::class, 'KlantId', 'Id');
    }
}
