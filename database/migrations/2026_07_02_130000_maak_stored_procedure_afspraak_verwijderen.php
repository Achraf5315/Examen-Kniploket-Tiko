<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Maakt de stored procedure spAfspraakVerwijderen aan.
 *
 * Deze procedure controleert eerst of de afspraak bestaat en verwijdert de
 * afspraak daarna definitief (hard delete, conform de wireframe:
 * "Deze actie kan niet ongedaan worden"). De DELETE gebruikt een JOIN met Klant.
 */
return new class extends Migration
{
    /**
     * Voer de migratie uit: maak de stored procedure aan.
     */
    public function up(): void
    {
        // Verwijder de procedure eerst als die al bestaat, zodat de migratie herhaalbaar is
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakVerwijderen');

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE spAfspraakVerwijderen(
                IN p_Id INT UNSIGNED
            )
            BEGIN
                DECLARE v_AantalGevonden INT DEFAULT 0;

                -- Controleer eerst of de afspraak bestaat (JOIN met Klant als integriteitscontrole)
                SELECT COUNT(*)
                INTO v_AantalGevonden
                FROM Afspraak a
                INNER JOIN Klant k ON k.Id = a.KlantId
                WHERE a.Id = p_Id;

                IF v_AantalGevonden = 0 THEN
                    -- Niet gevonden: geef een duidelijke foutmelding terug aan de applicatie
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'De afspraak is niet gevonden';
                END IF;

                -- Verwijder de afspraak definitief; dit kan niet ongedaan worden gemaakt
                DELETE a
                FROM Afspraak a
                INNER JOIN Klant k ON k.Id = a.KlantId
                WHERE a.Id = p_Id;
            END
            SQL);
    }

    /**
     * Draai de migratie terug: verwijder de stored procedure.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakVerwijderen');
    }
};
