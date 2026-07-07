<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Medewerker;

/**
 * Model voor de werktijden van een medewerker, gebruikt bij het inplannen van afspraken.
 */
class Werktijd extends TikoModel
{
    /** @use HasFactory<\Database\Factories\WerktijdFactory> */
    use HasFactory;

    // De databasetabel gebruikt PascalCase-namen (afwijkend van de Laravel-conventie).
    protected $table = 'Werktijd';

    public function medewerker(): BelongsTo
    {
        return $this->belongsTo(Medewerker::class, 'MedewerkerId', 'Id');
    }
}
