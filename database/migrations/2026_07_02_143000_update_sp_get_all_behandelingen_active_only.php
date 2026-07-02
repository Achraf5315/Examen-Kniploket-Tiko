<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Herstelt Sp_GetAllBehandelingen zodat het overzicht alleen ACTIEVE behandelingen toont.
     *
     * Bugfix: de oorspronkelijke procedure had geen filter op b.IsActief, waardoor een
     * (soft-)verwijderde behandeling in het overzicht bleef staan. Voor de happy path
     * "verwijderen" moet de behandeling juist verdwijnen, dus filteren we nu op IsActief = 1.
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
                ORDER BY b.Naam ASC;
            END
            SQL
        );
    }

    /**
     * Zet de procedure terug naar de versie zonder actief-filter.
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
