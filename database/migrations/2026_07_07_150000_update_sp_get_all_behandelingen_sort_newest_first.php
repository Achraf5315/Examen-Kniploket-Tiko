<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Wijzigt Sp_GetAllBehandelingen zodat de nieuwst toegevoegde behandeling bovenaan staat.
     *
     * Was: ORDER BY b.Naam ASC (alfabetisch). Nu: ORDER BY b.Id DESC, zodat het
     * overzicht consistent is met de andere overzichten (nieuwste eerst).
     */
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS Sp_GetAllBehandelingen');

        DB::unprepared(
            <<<'SQL'
            CREATE PROCEDURE Sp_GetAllBehandelingen()
            BEGIN
                /*
                 * Haalt alle ACTIEVE behandelingen op met gekoppelde producten.
                 * LEFT JOIN houdt behandelingen zonder producten zichtbaar;
                 * WHERE b.IsActief = 1 verbergt (soft-)verwijderde behandelingen.
                 * ORDER BY b.Id DESC toont de meest recent toegevoegde behandeling bovenaan.
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
                WHERE b.IsActief = 1
                GROUP BY
                    b.Id,
                    b.Naam,
                    b.Prijs,
                    b.DuurMinuten,
                    b.IsActief,
                    b.Opmerking
                ORDER BY b.Id DESC;
            END
            SQL
        );
    }

    /**
     * Zet de sortering terug naar alfabetisch op naam.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS Sp_GetAllBehandelingen');

        DB::unprepared(
            <<<'SQL'
            CREATE PROCEDURE Sp_GetAllBehandelingen()
            BEGIN
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
                WHERE b.IsActief = 1
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
};
