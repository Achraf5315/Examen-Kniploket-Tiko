<?php

namespace App\Models;

use Database\Factories\ProductPerLeverancierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;

/**
 * Koppeltabel-model tussen Product en Leverancier: bij welke leveranciers een product besteld kan worden.
 */
class ProductPerLeverancier extends TikoModel
{
    /** @use HasFactory<ProductPerLeverancierFactory> */
    use HasFactory;

    // De databasetabel gebruikt PascalCase-namen (afwijkend van de Laravel-conventie).
    protected $table = 'ProductPerLeverancier';

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'ProductId', 'Id');
    }

    public function leverancier(): BelongsTo
    {
        return $this->belongsTo(Leverancier::class, 'LeverancierId', 'Id');
    }
}
