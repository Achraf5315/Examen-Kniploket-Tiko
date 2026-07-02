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
        DB::unprepared('
            CREATE PROCEDURE GetAllBestellingen()
            BEGIN
                SELECT b.Id, b.Orderdatum, b.Status, p.Productnaam, k.Naam AS KlantNaam
                FROM Bestelling b
                JOIN Product p ON b.ProductId = p.Id
                JOIN Klant k ON b.KlantId = k.Id;
            END
        ');

        DB::unprepared('
            CREATE PROCEDURE FindBestellingById(IN bestellingId INT)
            BEGIN
                SELECT b.Id, b.Orderdatum, b.Status, p.Productnaam, k.Naam AS KlantNaam
                FROM Bestelling b
                JOIN Product p ON b.ProductId = p.Id
                JOIN Klant k ON b.KlantId = k.Id
                WHERE b.Id = bestellingId;
            END
        ');

        DB::unprepared('
            CREATE PROCEDURE CreateBestelling(IN productId INT, IN klantId INT, IN orderdatum DATE, IN status VARCHAR(255))
            BEGIN
                INSERT INTO Bestelling (ProductId, KlantId, Orderdatum, Status)
                VALUES (productId, klantId, orderdatum, status);
            END
        ');

        DB::unprepared('
            CREATE PROCEDURE UpdateBestelling(IN bestellingId INT, IN productId INT, IN klantId INT, IN orderdatum DATE, IN status VARCHAR(255))
            BEGIN
                UPDATE Bestelling
                SET ProductId = productId,
                    KlantId = klantId,
                    Orderdatum = orderdatum,
                    Status = status
                WHERE Id = bestellingId;
            END
        ');

        DB::unprepared('
            CREATE PROCEDURE DeleteBestelling(IN bestellingId INT)
            BEGIN
                DELETE FROM Bestelling WHERE Id = bestellingId;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
