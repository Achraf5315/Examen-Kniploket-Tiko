<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adres extends TikoModel
{
    /** @use HasFactory<\Database\Factories\AdresFactory> */
    use HasFactory;

    protected $table = 'Adres';

    public function klant(): BelongsTo
    {
        return $this->belongsTo(Klant::class, 'KlantId', 'Id');
    }
}
