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

        $sql = file_get_contents(base_path('database/createscript/stored_procedures/sp_afspraak_toevoegen.sql'));

        if ($sql === false) {
            throw new \RuntimeException('SQL-bestand voor spAfspraakToevoegen kon niet worden geladen.');
        }

        DB::unprepared($sql);
    }

    /**
     * Draai de migratie terug: verwijder de stored procedure.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS spAfspraakToevoegen');
    }
};
