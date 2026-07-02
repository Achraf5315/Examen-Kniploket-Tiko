<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Bestelling extends TikoModel
{
    /** @use HasFactory<\Database\Factories\BestellingFactory> */
    use HasFactory;

    protected $table = 'Bestelling';

    public function getAllBestellingen()
    {
        return DB::select('CALL sp_GetAllBestellingen()');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'ProductId', 'Id');
    }

    public function klant(): BelongsTo
    {
        return $this->belongsTo(Klant::class, 'KlantId', 'Id');
    }

    public function bestelregels(): HasMany
    {
        return $this->hasMany(Bestelregel::class, 'BestellingId', 'Id');
    }
}
