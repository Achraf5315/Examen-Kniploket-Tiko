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

        $sql = file_get_contents(base_path('database/createscript/stored_procedures/sp_afspraak_verwijderen.sql'));

        if ($sql === false) {
            throw new \RuntimeException('SQL-bestand voor spAfspraakVerwijderen kon niet worden geladen.');
        }

        DB::unprepared($sql);
    }

    /**
     * Draai de migratie terug: verwijder de stored procedure.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakVerwijderen');
    }
};
