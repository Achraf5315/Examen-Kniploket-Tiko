<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // DROP IF EXISTS voor idempotentie in RefreshDatabase (tests).
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetAllBestellingen');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_findBestellingById');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_CreateBestelling');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_UpdateBestelling');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_DeleteBestelling');

        DB::unprepared('
            CREATE PROCEDURE sp_GetAllBestellingen()
            BEGIN
                SELECT
                    b.Id,
                    b.ProductId,
                    b.KlantId,
                    b.Orderdatum,
                    b.VerwachteLeverdatum,
                    b.Status,
                    b.Opmerking,
                    p.Productnaam AS ProductNaam,
                    k.Naam AS KlantNaam
                FROM Bestelling b
                JOIN Product p ON b.ProductId = p.Id
                JOIN Klant k ON b.KlantId = k.Id;
            END
        ');

        DB::unprepared('
            CREATE PROCEDURE sp_findBestellingById(IN bestellingId INT)
            BEGIN
                SELECT
                    b.Id,
                    b.ProductId,
                    b.KlantId,
                    b.Orderdatum,
                    b.VerwachteLeverdatum,
                    b.Status,
                    b.Opmerking,
                    p.Productnaam AS ProductNaam,
                    k.Naam AS KlantNaam
                FROM Bestelling b
                JOIN Product p ON b.ProductId = p.Id
                JOIN Klant k ON b.KlantId = k.Id
                WHERE b.Id = bestellingId;
            END
        ');

        DB::unprepared('
            CREATE PROCEDURE sp_CreateBestelling(
                IN productId INT,
                IN klantId INT,
                IN orderdatum DATE,
                IN verwachteLeverdatum DATE,
                IN status VARCHAR(255)
            )
            BEGIN
                INSERT INTO Bestelling (
                    ProductId,
                    KlantId,
                    Orderdatum,
                    VerwachteLeverdatum,
                    Status,
                    IsActief,
                    DatumAangemaakt,
                    DatumGewijzigd
                )
                VALUES (
                    productId,
                    klantId,
                    orderdatum,
                    verwachteLeverdatum,
                    status,
                    1,
                    NOW(),
                    NOW()
                );
            END
        ');

        DB::unprepared('
            CREATE PROCEDURE sp_UpdateBestelling(
                IN bestellingId INT,
                IN productId INT,
                IN klantId INT,
                IN orderdatum DATE,
                IN verwachteLeverdatum DATE,
                IN status VARCHAR(255),
                IN opmerking VARCHAR(255)
            )
            BEGIN
                UPDATE Bestelling
                SET ProductId = productId,
                    KlantId = klantId,
                    Orderdatum = orderdatum,
                    VerwachteLeverdatum = verwachteLeverdatum,
                    Status = status,
                    Opmerking = opmerking,
                    DatumGewijzigd = NOW()
                WHERE Id = bestellingId;
            END
        ');

        DB::unprepared('
            CREATE PROCEDURE sp_DeleteBestelling(IN bestellingId INT)
            BEGIN
                UPDATE Bestelling
                SET IsActief = 0,
                    DatumGewijzigd = NOW()
                WHERE Id = bestellingId;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetAllBestellingen');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_findBestellingById');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_CreateBestelling');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_UpdateBestelling');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_DeleteBestelling');
    }
};
