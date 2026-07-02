<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Maakt de stored procedures spAfspraakDetails en spAfspraakWijzigen aan.
 *
 * spAfspraakDetails haalt één afspraak op met alle gekoppelde gegevens (JOINs),
 * bijvoorbeeld voor het vooraf invullen van het wijzigformulier.
 * spAfspraakWijzigen werkt een afspraak bij, nadat (via een JOIN met Behandeling)
 * is gecontroleerd dat de nieuwe tijden niet overlappen met andere afspraken.
 */
return new class extends Migration
{
    /**
     * Voer de migratie uit: maak beide stored procedures aan.
     */
    public function up(): void
    {
        // Verwijder de procedures eerst als die al bestaan, zodat de migratie herhaalbaar is
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakDetails');
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakWijzigen');

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE spAfspraakDetails(
                IN p_Id INT UNSIGNED
            )
            BEGIN
                -- Details van één actieve afspraak inclusief klant, medewerker en behandeling
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
                WHERE a.Id = p_Id
                  AND a.IsActief = 1;
            END
            SQL);

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE spAfspraakWijzigen(
                IN p_Id INT UNSIGNED,
                IN p_KlantId INT UNSIGNED,
                IN p_MedewerkerId INT UNSIGNED,
                IN p_BehandelingId INT UNSIGNED,
                IN p_Datum DATE,
                IN p_Starttijd TIME
            )
            BEGIN
                DECLARE v_AantalGevonden INT DEFAULT 0;
                DECLARE v_AantalOverlappend INT DEFAULT 0;
                DECLARE v_NieuweEindtijd TIME;

                -- Controleer eerst of de te wijzigen afspraak bestaat
                SELECT COUNT(*)
                INTO v_AantalGevonden
                FROM Afspraak
                WHERE Id = p_Id
                  AND IsActief = 1;

                IF v_AantalGevonden = 0 THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'De afspraak is niet gevonden';
                END IF;

                -- Bereken de nieuwe eindtijd op basis van de behandelingsduur
                SELECT ADDTIME(p_Starttijd, SEC_TO_TIME(b.DuurMinuten * 60))
                INTO v_NieuweEindtijd
                FROM Behandeling b
                WHERE b.Id = p_BehandelingId;

                -- Controleer op overlap met andere afspraken van dezelfde medewerker
                -- op dezelfde datum (JOIN met Behandeling voor hun eindtijden).
                -- De eigen afspraak telt uiteraard niet mee.
                SELECT COUNT(*)
                INTO v_AantalOverlappend
                FROM Afspraak a
                INNER JOIN Behandeling b ON b.Id = a.BehandelingId
                WHERE a.MedewerkerId = p_MedewerkerId
                  AND a.Datum = p_Datum
                  AND a.Id <> p_Id
                  AND a.IsActief = 1
                  AND a.Status <> 'Geannuleerd'
                  AND p_Starttijd < ADDTIME(a.Starttijd, SEC_TO_TIME(b.DuurMinuten * 60))
                  AND v_NieuweEindtijd > a.Starttijd;

                IF v_AantalOverlappend > 0 THEN
                    -- Overlap gevonden: geef een duidelijke foutmelding terug aan de applicatie
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'De afspraak overlapt met een bestaande afspraak';
                END IF;

                -- Geen overlap: werk de afspraak bij (status en opmerking blijven ongewijzigd)
                UPDATE Afspraak
                SET KlantId = p_KlantId,
                    MedewerkerId = p_MedewerkerId,
                    BehandelingId = p_BehandelingId,
                    Datum = p_Datum,
                    Starttijd = p_Starttijd,
                    DatumGewijzigd = NOW()
                WHERE Id = p_Id;
            END
            SQL);
    }

    /**
     * Draai de migratie terug: verwijder beide stored procedures.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakWijzigen');
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakDetails');
    }
};
