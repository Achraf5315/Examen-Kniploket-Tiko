<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model voor producten die Kniploket Tiko verkoopt en/of gebruikt bij behandelingen.
 *
 * De meeste CRUD-bewerkingen op producten lopen via stored procedures
 * (zie ProductController), dit model regelt vooral de Eloquent-relaties.
 */
class Product extends TikoModel
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    // De databasetabel gebruikt PascalCase-namen (afwijkend van de Laravel-conventie).
    protected $table = 'Product';

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'CategorieId', 'Id');
    }

    // Veel-op-veel: bij welke leveranciers dit product besteld kan worden.
    public function leveranciers(): BelongsToMany
    {
        return $this->belongsToMany(Leverancier::class, 'ProductPerLeverancier', 'ProductId', 'LeverancierId')
            ->withPivot(['Id', 'IsActief', 'Opmerking', 'DatumAangemaakt', 'DatumGewijzigd']);
    }

    // Veel-op-veel: bij welke behandelingen dit product gebruikt wordt.
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
