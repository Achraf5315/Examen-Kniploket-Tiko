<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Maakt de stored procedure spAfspraakOverzicht aan.
 *
 * Deze procedure haalt alle actieve afspraken op inclusief de gekoppelde
 * klant-, medewerker- en behandelingsgegevens (via JOINs).
 */
return new class extends Migration
{
    /**
     * Voer de migratie uit: maak de stored procedure aan.
     */
    public function up(): void
    {
        // Verwijder de procedure eerst als die al bestaat, zodat de migratie herhaalbaar is
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakOverzicht');

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE spAfspraakOverzicht()
            BEGIN
                -- Overzicht van alle actieve afspraken met klant, medewerker en behandeling
                SELECT
                    a.Id,
                    a.KlantId,
                    k.Naam AS KlantNaam,
                    a.MedewerkerId,
                    m.Naam AS MedewerkerNaam,
                    a.BehandelingId,
                    b.Naam AS BehandelingNaam,
                    b.DuurMinuten,
                    a.Datum,
                    a.Starttijd,
                    -- De eindtijd wordt berekend met de duur van de behandeling
                    ADDTIME(a.Starttijd, SEC_TO_TIME(b.DuurMinuten * 60)) AS Eindtijd,
                    a.Status,
                    a.Opmerking
                FROM Afspraak a
                INNER JOIN Klant k ON k.Id = a.KlantId
                INNER JOIN Medewerker m ON m.Id = a.MedewerkerId
                INNER JOIN Behandeling b ON b.Id = a.BehandelingId
                WHERE a.IsActief = 1
                ORDER BY a.Datum ASC, a.Starttijd ASC;
            END
            SQL);
    }

    /**
     * Draai de migratie terug: verwijder de stored procedure.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakOverzicht');
    }
};
