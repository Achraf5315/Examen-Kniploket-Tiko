<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Basisklasse voor alle Eloquent-modellen in dit project.
 *
 * Regelt de gedeelde afwijkingen van de Laravel-conventie: de database
 * gebruikt overal 'Id' als primaire sleutel en Nederlandse PascalCase-
 * kolommen voor de timestamps en de actief/inactief-vlag.
 */
abstract class TikoModel extends Model
{
    protected $primaryKey = 'Id';

    // Nieuwe records zijn standaard actief, tenzij expliciet anders opgegeven.
    protected $attributes = [
        'IsActief' => true,
    ];

    // Geen mass-assignment whitelist nodig: de CRUD-acties lopen via stored
    // procedures en Form Requests regelen de validatie van formulierinvoer.
    protected $guarded = [];

    public const CREATED_AT = 'DatumAangemaakt';

    public const UPDATED_AT = 'DatumGewijzigd';

    protected function casts(): array
    {
        return [
            'IsActief' => 'boolean',
            'DatumAangemaakt' => 'datetime',
            'DatumGewijzigd' => 'datetime',
        ];
    }
}
