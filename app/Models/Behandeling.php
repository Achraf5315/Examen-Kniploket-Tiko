<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Behandeling extends TikoModel
{
    /** @use HasFactory<\Database\Factories\BehandelingFactory> */
    use HasFactory;

    protected $table = 'Behandeling';

    public function producten(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'BehandelingPerProduct', 'BehandelingId', 'ProductId')
            ->withPivot(['Id', 'Aantal', 'IsActief', 'Opmerking', 'DatumAangemaakt', 'DatumGewijzigd']);
    }

    public function medewerkers(): BelongsToMany
    {
        return $this->belongsToMany(Medewerker::class, 'MedewerkerPerBehandeling', 'BehandelingId', 'MedewerkerId')
            ->withPivot(['Id', 'IsActief', 'Opmerking', 'DatumAangemaakt', 'DatumGewijzigd']);
    }

    public function afspraken(): HasMany
    {
        return $this->hasMany(Afspraak::class, 'BehandelingId', 'Id');
    }
}
