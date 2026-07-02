<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Maakt de stored procedure spAfspraakToevoegen aan.
 *
 * Deze procedure controleert eerst (via een JOIN met Behandeling) of de nieuwe
 * afspraak overlapt met een bestaande afspraak van dezelfde medewerker.
 * Bij overlap wordt een SIGNAL-fout teruggegeven, anders wordt de afspraak toegevoegd.
 */
return new class extends Migration
{
    /**
     * Voer de migratie uit: maak de stored procedure aan.
     */
    public function up(): void
    {
        // Verwijder de procedure eerst als die al bestaat, zodat de migratie herhaalbaar is
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakToevoegen');

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE spAfspraakToevoegen(
                IN p_KlantId INT UNSIGNED,
                IN p_MedewerkerId INT UNSIGNED,
                IN p_BehandelingId INT UNSIGNED,
                IN p_Datum DATE,
                IN p_Starttijd TIME
            )
            BEGIN
                DECLARE v_AantalOverlappend INT DEFAULT 0;
                DECLARE v_NieuweEindtijd TIME;

                -- Bereken de eindtijd van de nieuwe afspraak op basis van de behandelingsduur
                SELECT ADDTIME(p_Starttijd, SEC_TO_TIME(b.DuurMinuten * 60))
                INTO v_NieuweEindtijd
                FROM Behandeling b
                WHERE b.Id = p_BehandelingId;

                -- Controleer op overlap met bestaande afspraken van dezelfde medewerker
                -- op dezelfde datum (JOIN met Behandeling voor de eindtijd van die afspraken).
                -- Geannuleerde en inactieve afspraken tellen niet mee.
                SELECT COUNT(*)
                INTO v_AantalOverlappend
                FROM Afspraak a
                INNER JOIN Behandeling b ON b.Id = a.BehandelingId
                WHERE a.MedewerkerId = p_MedewerkerId
                  AND a.Datum = p_Datum
                  AND a.IsActief = 1
                  AND a.Status <> 'Geannuleerd'
                  AND p_Starttijd < ADDTIME(a.Starttijd, SEC_TO_TIME(b.DuurMinuten * 60))
                  AND v_NieuweEindtijd > a.Starttijd;

                IF v_AantalOverlappend > 0 THEN
                    -- Overlap gevonden: geef een duidelijke foutmelding terug aan de applicatie
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'De afspraak overlapt met een bestaande afspraak';
                END IF;

                -- Geen overlap: voeg de afspraak toe met de status Gereserveerd
                INSERT INTO Afspraak
                    (KlantId, MedewerkerId, BehandelingId, Datum, Starttijd, Status, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
                VALUES
                    (p_KlantId, p_MedewerkerId, p_BehandelingId, p_Datum, p_Starttijd, 'Gereserveerd', 1, NULL, NOW(), NOW());

                -- Geef het Id van de nieuwe afspraak terug aan de applicatie
                SELECT LAST_INSERT_ID() AS Id;
            END
            SQL);
    }

    /**
     * Draai de migratie terug: verwijder de stored procedure.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakToevoegen');
    }
};
