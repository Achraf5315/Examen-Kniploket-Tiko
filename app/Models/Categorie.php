<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model voor productcategorieën, gebruikt om producten in te delen en te filteren.
 */
class Categorie extends TikoModel
{
    /** @use HasFactory<\Database\Factories\CategorieFactory> */
    use HasFactory;

    // De databasetabel gebruikt PascalCase-namen (afwijkend van de Laravel-conventie).
    protected $table = 'Categorie';

    public function producten(): HasMany
    {
        return $this->hasMany(Product::class, 'CategorieId', 'Id');
    }
}
