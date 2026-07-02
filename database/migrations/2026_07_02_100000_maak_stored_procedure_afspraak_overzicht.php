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

        $sql = file_get_contents(base_path('database/createscript/stored_procedures/sp_afspraak_overzicht.sql'));

        if ($sql === false) {
            throw new \RuntimeException('SQL-bestand voor spAfspraakOverzicht kon niet worden geladen.');
        }

        DB::unprepared($sql);
    }

    /**
     * Draai de migratie terug: verwijder de stored procedure.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakOverzicht');
    }
};
