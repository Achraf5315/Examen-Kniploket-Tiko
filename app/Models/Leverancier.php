<?php

namespace App\Models;

use Database\Factories\LeverancierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model voor leveranciers waarbij producten besteld kunnen worden.
 */
class Leverancier extends TikoModel
{
    /** @use HasFactory<LeverancierFactory> */
    use HasFactory;

    // De databasetabel gebruikt PascalCase-namen (afwijkend van de Laravel-conventie).
    protected $table = 'Leverancier';

    // Veel-op-veel: welke producten deze leverancier levert.
    public function producten(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'ProductPerLeverancier', 'LeverancierId', 'ProductId')
            ->withPivot(['Id', 'IsActief', 'Opmerking', 'DatumAangemaakt', 'DatumGewijzigd']);
    }
}
