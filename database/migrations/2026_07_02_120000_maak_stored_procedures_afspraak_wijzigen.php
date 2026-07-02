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

        $detailsSql = file_get_contents(base_path('database/createscript/stored_procedures/sp_afspraak_details.sql'));
        $wijzigenSql = file_get_contents(base_path('database/createscript/stored_procedures/sp_afspraak_wijzigen.sql'));

        if ($detailsSql === false || $wijzigenSql === false) {
            throw new \RuntimeException('Een SQL-bestand voor de afspraakprocedures kon niet worden geladen.');
        }

        DB::unprepared($detailsSql);
        DB::unprepared($wijzigenSql);
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
