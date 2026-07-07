<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends TikoModel
{
    /** @use HasFactory<\Database\Factories\CategorieFactory> */
    use HasFactory;

    protected $table = 'Categorie';

    public function producten(): HasMany
    {
        return $this->hasMany(Product::class, 'CategorieId', 'Id');
    }
}
