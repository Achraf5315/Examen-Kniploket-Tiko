<?php

namespace App\Models;

use Database\Factories\BestellingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Bestelling extends TikoModel
{
    /** @use HasFactory<BestellingFactory> */
    use HasFactory;

    protected $table = 'Bestelling';

    public function getAllBestellingen()
    {
        return DB::select('CALL sp_GetAllBestellingen()');
    }

    public function createBestelling(array $data): void
    {
        DB::select(
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

    public function updateBestelling(int $id, array $data): void
    {
        // Stored procedure verwacht: Id, KlantId, ProductId, Orderdatum, VerwachteLeverdatum, Status, Opmerking
        DB::select(
            'CALL sp_UpdateBestelling(?, ?, ?, ?, ?, ?, ?)',
            [
                $id,
                $data['KlantId'],
                $data['ProductId'],
                $data['Orderdatum'],
                $data['VerwachteLeverdatum'],
                $data['Status'],
                $data['Opmerking'] ?? null,
            ]
        );
    }

    public function deleteBestelling(int $id): void
    {
        DB::select('CALL sp_DeleteBestelling(?)', [$id]);
    }

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
