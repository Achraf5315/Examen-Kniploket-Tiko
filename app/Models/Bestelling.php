<?php

namespace App\Models;

use Database\Factories\BestellingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Model voor bestellingen bij leveranciers.
 *
 * Alle CRUD-bewerkingen lopen via stored procedures (exameneis), zodat
 * validatie- en bedrijfslogica in de database gecentraliseerd blijft.
 */
class Bestelling extends TikoModel
{
    /** @use HasFactory<BestellingFactory> */
    use HasFactory;

    // De databasetabel gebruikt PascalCase-namen (afwijkend van de Laravel-conventie).
    protected $table = 'Bestelling';

    /**
     * Haalt alle bestellingen op via de stored procedure sp_GetAllBestellingen.
     *
     * De procedure joint direct met Product en Klant zodat de namen
     * (in plaats van alleen de Id's) meteen beschikbaar zijn voor de view.
     */
    public function getAllBestellingen()
    {
        return DB::select('CALL sp_GetAllBestellingen()');
    }

    /**
     * Voegt een nieuwe bestelling toe via de stored procedure sp_CreateBestelling.
     */
    public function createBestelling(array $data): void
    {
        DB::statement(
            'CALL sp_CreateBestelling(?, ?, ?, ?, ?)',
            [
                $data['ProductNaam'],
                $data['KlantNaam'],
                $data['Orderdatum'],
                $data['VerwachteLeverdatum'],
                $data['Status'],
            ]
        );
    }

    /**
     * Werkt een bestaande bestelling bij via de stored procedure sp_UpdateBestelling.
     */
    public function updateBestelling(int $id, array $data): void
    {
        // Stored procedure verwacht: Id, ProductId, KlantId, Orderdatum, VerwachteLeverdatum, Status, Opmerking
        DB::statement(
            'CALL sp_UpdateBestelling(?, ?, ?, ?, ?, ?, ?)',
            [
                $id,
                $data['ProductId'],
                $data['KlantId'],
                $data['Orderdatum'],
                $data['VerwachteLeverdatum'],
                $data['Status'],
                $data['Opmerking'] ?? null,
            ]
        );
    }

    /**
     * Verwijdert een bestelling via de stored procedure sp_DeleteBestelling.
     */
    public function deleteBestelling(int $id): void
    {
        DB::statement('CALL sp_DeleteBestelling(?)', [$id]);
    }

    /**
     * Zoekt één bestelling op via de stored procedure sp_findBestellingById.
     *
     * Wordt gebruikt om het wijzigformulier te vullen met de huidige gegevens.
     */
    public function findBestellingById(int $id)
    {
        return DB::selectOne('CALL sp_findBestellingById(?)', [$id]);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'ProductId', 'Id');
    }

    public function klant(): BelongsTo
    {
        return $this->belongsTo(Klant::class, 'KlantId', 'Id');
    }

    public function bestelregels(): HasMany
    {
        return $this->hasMany(Bestelregel::class, 'BestellingId', 'Id');
    }
}
