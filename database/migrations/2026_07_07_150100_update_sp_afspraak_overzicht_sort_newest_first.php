<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Wijzigt spAfspraakOverzicht zodat de nieuwst aangemaakte afspraak bovenaan staat.
     *
     * Was: ORDER BY a.Datum ASC, a.Starttijd ASC (chronologisch). Nu: ORDER BY a.Id DESC,
     * zodat het overzicht consistent is met de andere overzichten (nieuwste eerst).
     */
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakOverzicht');

        DB::unprepared(
            <<<'SQL'
            CREATE PROCEDURE spAfspraakOverzicht()
            BEGIN
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
                    ADDTIME(a.Starttijd, SEC_TO_TIME(b.DuurMinuten * 60)) AS Eindtijd,
                    a.Status,
                    a.Opmerking
                FROM Afspraak a
                INNER JOIN Klant k ON k.Id = a.KlantId
                INNER JOIN Medewerker m ON m.Id = a.MedewerkerId
                INNER JOIN Behandeling b ON b.Id = a.BehandelingId
                WHERE a.IsActief = 1
                ORDER BY a.Id DESC;
            END
            SQL
        );
    }

    /**
     * Zet de sortering terug naar chronologisch op datum en starttijd.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakOverzicht');

        DB::unprepared(
            <<<'SQL'
            CREATE PROCEDURE spAfspraakOverzicht()
            BEGIN
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
            SQL
        );
    }
};
