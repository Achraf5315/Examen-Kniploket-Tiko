<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class TikoModel extends Model
{
    protected $primaryKey = 'Id';

    protected $attributes = [
        'IsActief' => true,
    ];

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
