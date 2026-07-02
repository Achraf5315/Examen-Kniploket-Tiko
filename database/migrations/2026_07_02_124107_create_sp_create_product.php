<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_CreateProduct');

        $sql = file_get_contents(base_path('database/createscript/Storeproductrequest.sql'));

        if ($sql === false) {
            throw new \RuntimeException('SQL-bestand voor sp_CreateProduct kon niet worden geladen.');
        }

        DB::unprepared($sql);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_CreateProduct');
    }
};
