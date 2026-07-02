<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Behandeling extends TikoModel
{
    /** @use HasFactory<\Database\Factories\BehandelingFactory> */
    use HasFactory;

    // De databasetabel gebruikt PascalCase-namen (afwijkend van de Laravel-conventie),
    // daarom leggen we tabel- en primaire sleutelnaam expliciet vast.
    protected $table = 'Behandeling';

    protected $primaryKey = 'Id';

    // Velden die via mass-assignment gevuld mogen worden. De CRUD-acties lopen via
    // stored procedures, maar deze lijst blijft nodig voor factory/seeder en tests.
    protected $fillable = [
        'Naam',
        'Prijs',
        'DuurMinuten',
        'IsActief',
        'Opmerking',
    ];

    /**
     * Haalt het overzicht op via de SQL Stored Procedure Sp_GetAllBehandelingen.
     *
     * We gebruiken een stored procedure (exameneis) zodat de JOIN met producten en
     * de sortering in de database gebeuren en niet in PHP.
     */
    public static function getAllViaStoredProcedure(): array
    {
        // Technische log zodat we kunnen zien wanneer het overzicht wordt opgehaald.
        Log::debug('Behandelingoverzicht opgehaald via Sp_GetAllBehandelingen');

        return DB::select('CALL Sp_GetAllBehandelingen()');
    }

    /**
     * Voegt een behandeling toe via de stored procedure Sp_InsertBehandeling.
     *
     * De procedure regelt binnen één transactie zowel de insert van de behandeling
     * als de optionele koppeling aan een product, zodat de data altijd consistent is.
     */
    public static function insertViaProcedure(
        string $naam,
        float $prijs,
        int $duurMinuten,
        ?string $opmerking,
        ?int $productId
    ): bool {
        // Technische log met de parameters die naar de procedure gaan (voor debugging).
        Log::debug('Sp_InsertBehandeling aangeroepen', [
            'naam' => $naam,
            'prijs' => $prijs,
            'duur_minuten' => $duurMinuten,
            'product_id' => $productId,
        ]);

        return DB::statement(
            'CALL Sp_InsertBehandeling(?, ?, ?, ?, ?)',
            [$naam, $prijs, $duurMinuten, $opmerking, $productId]
        );
    }

    /**
     * Werkt een behandeling bij via de stored procedure Sp_UpdateBehandeling.
     *
     * De procedure zet bestaande productkoppelingen op inactief en koppelt eventueel
     * het nieuw gekozen product, opnieuw binnen één transactie.
     */
    public static function updateViaProcedure(
        int $id,
        string $naam,
        float $prijs,
        int $duurMinuten,
        ?string $opmerking,
        ?int $productId
    ): bool {
        // Technische log met het id en de nieuwe waarden (voor debugging).
        Log::debug('Sp_UpdateBehandeling aangeroepen', [
            'id' => $id,
            'naam' => $naam,
            'prijs' => $prijs,
            'duur_minuten' => $duurMinuten,
            'product_id' => $productId,
        ]);

        return DB::statement(
            'CALL Sp_UpdateBehandeling(?, ?, ?, ?, ?, ?)',
            [$id, $naam, $prijs, $duurMinuten, $opmerking, $productId]
        );
    }

    /**
     * Soft-deletet een behandeling via de stored procedure Sp_DeleteBehandeling.
     *
     * We verwijderen niet fysiek maar zetten IsActief op 0, zodat historische
     * afspraken naar de behandeling blijven verwijzen (referentiële integriteit).
     */
    public static function deleteViaProcedure(int $id): bool
    {
        // Technische log zodat verwijderacties traceerbaar zijn.
        Log::debug('Sp_DeleteBehandeling aangeroepen', ['id' => $id]);

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
