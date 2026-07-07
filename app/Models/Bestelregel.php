<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model voor een regel binnen een bestelling (bijv. één productlijn met aantal).
 */
class Bestelregel extends TikoModel
{
    /** @use HasFactory<\Database\Factories\BestelregelFactory> */
    use HasFactory;

    // De databasetabel gebruikt PascalCase-namen (afwijkend van de Laravel-conventie).
    protected $table = 'Bestelregel';

    public function bestelling(): BelongsTo
    {
        return $this->belongsTo(Bestelling::class, 'BestellingId', 'Id');
    }
}
