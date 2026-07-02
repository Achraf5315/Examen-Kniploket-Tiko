<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Behandeling extends TikoModel
{
    /** @use HasFactory<\Database\Factories\BehandelingFactory> */
    use HasFactory;

    protected $table = 'Behandeling';

    protected $primaryKey = 'Id';

    protected $fillable = [
        'Naam',
        'Prijs',
        'DuurMinuten',
        'IsActief',
        'Opmerking',
    ];

    /**
     * Haalt het overzicht op via de SQL Stored Procedure.
     */
    public static function getAllViaStoredProcedure(): array
    {
        return DB::select('CALL Sp_GetAllBehandelingen()');
    }

    /**
     * Voegt een behandeling toe via de stored procedure.
     */
    public static function insertViaProcedure(
        string $naam,
        float $prijs,
        int $duurMinuten,
        ?string $opmerking,
        ?int $productId
    ): bool {
        return DB::statement(
            'CALL Sp_InsertBehandeling(?, ?, ?, ?, ?)',
            [$naam, $prijs, $duurMinuten, $opmerking, $productId]
        );
    }

    /**
     * Werkt een behandeling bij via de stored procedure.
     */
    public static function updateViaProcedure(
        int $id,
        string $naam,
        float $prijs,
        int $duurMinuten,
        ?string $opmerking,
        ?int $productId
    ): bool {
        return DB::statement(
            'CALL Sp_UpdateBehandeling(?, ?, ?, ?, ?, ?)',
            [$id, $naam, $prijs, $duurMinuten, $opmerking, $productId]
        );
    }

    /**
     * Soft-deletet een behandeling via de stored procedure.
     */
    public static function deleteViaProcedure(int $id): bool
    {
        return DB::statement('CALL Sp_DeleteBehandeling(?)', [$id]);
    }

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
