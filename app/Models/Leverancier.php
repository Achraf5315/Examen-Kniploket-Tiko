<?php

namespace App\Models;

use Database\Factories\LeverancierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Leverancier extends TikoModel
{
    /** @use HasFactory<LeverancierFactory> */
    use HasFactory;

    protected $table = 'Leverancier';

    public function producten(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'ProductPerLeverancier', 'LeverancierId', 'ProductId')
            ->withPivot(['Id', 'IsActief', 'Opmerking', 'DatumAangemaakt', 'DatumGewijzigd']);
    }
}
