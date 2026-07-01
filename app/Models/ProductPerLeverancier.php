<?php

namespace App\Models;

use Database\Factories\ProductPerLeverancierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;

class ProductPerLeverancier extends TikoModel
{
    /** @use HasFactory<ProductPerLeverancierFactory> */
    use HasFactory;

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
