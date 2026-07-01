<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Medewerker;

class Werktijd extends TikoModel
{
    /** @use HasFactory<\Database\Factories\WerktijdFactory> */
    use HasFactory;

    protected $table = 'Werktijd';

    public function medewerker(): BelongsTo
    {
        return $this->belongsTo(Medewerker::class, 'MedewerkerId', 'Id');
    }
}
