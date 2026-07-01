<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bestelregel extends TikoModel
{
    /** @use HasFactory<\Database\Factories\BestelregelFactory> */
    use HasFactory;

    protected $table = 'Bestelregel';

    public function bestelling(): BelongsTo
    {
        return $this->belongsTo(Bestelling::class, 'BestellingId', 'Id');
    }
}
