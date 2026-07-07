<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates helper stored procedures for categories and suppliers:
     * - sp_GetCategories: Get all active categories
     * - sp_GetLeveranciers: Get all active suppliers
     *
     * These are used by ProductController for dropdown menus.
     */
    public function up(): void
    {
        // Drop existing procedures for idempotency
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetCategories');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetLeveranciers');

        // ==========================================
        // PROCEDURE 1: Get all active categories
        // Columns: Id, Naam, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd
        // ==========================================
        DB::unprepared('
            CREATE PROCEDURE sp_GetCategories()
            BEGIN
                SELECT
                    Id,
                    Naam,
                    IsActief,
                    Opmerking,
                    DatumAangemaakt,
                    DatumGewijzigd
                FROM Categorie
                WHERE IsActief = 1
                ORDER BY Naam ASC;
            END
        ');

        // ==========================================
        // PROCEDURE 2: Get all active suppliers
        // Columns: Id, Naam, Telefoonnummer, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd
        // ==========================================
        DB::unprepared('
            CREATE PROCEDURE sp_GetLeveranciers()
            BEGIN
                SELECT
                    Id,
                    Naam,
                    Telefoonnummer,
                    IsActief,
                    Opmerking,
                    DatumAangemaakt,
                    DatumGewijzigd
                FROM Leverancier
                WHERE IsActief = 1
                ORDER BY Naam ASC;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetCategories');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetLeveranciers');
    }
};