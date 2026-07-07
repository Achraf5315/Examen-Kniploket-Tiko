<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Wijzigt sp_GetAllBestellingen zodat de nieuwst aangemaakte bestelling bovenaan staat.
     *
     * Was: ORDER BY b.DatumGewijzigd DESC (laatst gewijzigd bovenaan). Nu: ORDER BY b.Id DESC,
     * zodat een bewerking van een oude bestelling deze niet naar boven verplaatst.
     */
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetAllBestellingen');

        DB::unprepared(
            <<<'SQL'
            CREATE PROCEDURE sp_GetAllBestellingen()
            BEGIN
                SELECT
                    b.Id,
                    b.ProductId,
                    p.Productnaam AS ProductNaam,
                    b.KlantId,
                    k.Naam AS KlantNaam,
                    b.Orderdatum,
                    b.VerwachteLeverdatum,
                    b.Status,
                    b.IsActief,
                    b.Opmerking,
                    b.DatumAangemaakt,
                    b.DatumGewijzigd
                FROM Bestelling b
                LEFT JOIN Product p ON b.ProductId = p.Id
                LEFT JOIN Klant k ON b.KlantId = k.Id
                ORDER BY b.Id DESC;
            END
            SQL
        );
    }

    /**
     * Zet de sortering terug naar laatst gewijzigd bovenaan.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetAllBestellingen');

        DB::unprepared(
            <<<'SQL'
            CREATE PROCEDURE sp_GetAllBestellingen()
            BEGIN
                SELECT
                    b.Id,
                    b.ProductId,
                    p.Productnaam AS ProductNaam,
                    b.KlantId,
                    k.Naam AS KlantNaam,
                    b.Orderdatum,
                    b.VerwachteLeverdatum,
                    b.Status,
                    b.IsActief,
                    b.Opmerking,
                    b.DatumAangemaakt,
                    b.DatumGewijzigd
                FROM Bestelling b
                LEFT JOIN Product p ON b.ProductId = p.Id
                LEFT JOIN Klant k ON b.KlantId = k.Id
                ORDER BY b.DatumGewijzigd DESC;
            END
            SQL
        );
    }
};
