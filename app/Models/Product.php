<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends TikoModel
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $table = 'Product';

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'CategorieId', 'Id');
    }

    public function leveranciers(): BelongsToMany
    {
        return $this->belongsToMany(Leverancier::class, 'ProductPerLeverancier', 'ProductId', 'LeverancierId')
            ->withPivot(['Id', 'IsActief', 'Opmerking', 'DatumAangemaakt', 'DatumGewijzigd']);
    }

    public function behandelingen(): BelongsToMany
    {
        return $this->belongsToMany(Behandeling::class, 'BehandelingPerProduct', 'ProductId', 'BehandelingId')
            ->withPivot(['Id', 'Aantal', 'IsActief', 'Opmerking', 'DatumAangemaakt', 'DatumGewijzigd']);
    }

    public function bestellingen(): HasMany
    {
        return $this->hasMany(Bestelling::class, 'ProductId', 'Id');
    }
}
