<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verwijder de procedure eerst, zodat de migratie herhaalbaar blijft.
        DB::unprepared('DROP PROCEDURE IF EXISTS Sp_GetAllBehandelingen');

        // Maak de stored procedure aan voor het behandelingsoverzicht met productnamen.
        DB::unprepared(
            <<<'SQL'
            CREATE PROCEDURE Sp_GetAllBehandelingen()
            BEGIN
                /*
                 * Haalt alle behandelingen op met gekoppelde producten.
                 * LEFT JOIN zorgt ervoor dat ook behandelingen zonder producten zichtbaar blijven.
                 */
                SELECT
                    b.Id,
                    b.Naam,
                    b.Prijs,
                    b.DuurMinuten,
                    b.IsActief,
                    b.Opmerking,
                    GROUP_CONCAT(p.Productnaam SEPARATOR ', ') AS Producten
                FROM Behandeling AS b
                LEFT JOIN BehandelingPerProduct AS bpp ON bpp.BehandelingId = b.Id
                    AND bpp.IsActief = 1
                LEFT JOIN Product AS p ON p.Id = bpp.ProductId
                    AND p.IsActief = 1
                GROUP BY
                    b.Id,
                    b.Naam,
                    b.Prijs,
                    b.DuurMinuten,
                    b.IsActief,
                    b.Opmerking
                ORDER BY b.Naam ASC;
            END
            SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Verwijder de procedure bij rollback.
        DB::unprepared('DROP PROCEDURE IF EXISTS Sp_GetAllBehandelingen');
    }
};
