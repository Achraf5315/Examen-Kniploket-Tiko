<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class BehandelingPerProduct extends TikoModel
{
    /** @use HasFactory<\Database\Factories\BehandelingPerProductFactory> */
    use HasFactory;

    protected $table = 'BehandelingPerProduct';

    public function behandeling(): BelongsTo
    {
        return $this->belongsTo(Behandeling::class, 'BehandelingId', 'Id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'ProductId', 'Id');
    }
}
