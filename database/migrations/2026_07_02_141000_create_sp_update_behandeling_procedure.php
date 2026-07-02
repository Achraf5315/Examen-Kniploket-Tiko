<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Maakt de stored procedure Sp_UpdateBehandeling aan.
     *
     * De procedure werkt de behandeling bij en herstelt de productkoppeling: bestaande
     * koppelingen worden op inactief gezet en de nieuw gekozen koppeling wordt toegevoegd.
     */
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS Sp_UpdateBehandeling');

        DB::unprepared(
            <<<'SQL'
            CREATE PROCEDURE Sp_UpdateBehandeling(
                IN p_Id INT,
                IN p_Naam VARCHAR(100),
                IN p_Prijs DECIMAL(6, 2),
                IN p_DuurMinuten SMALLINT,
                IN p_Opmerking VARCHAR(255),
                IN p_ProductId INT
            )
            BEGIN
                -- Wijziging en herkoppeling binnen één transactie voor consistente data.
                START TRANSACTION;

                UPDATE Behandeling
                SET Naam = p_Naam,
                    Prijs = p_Prijs,
                    DuurMinuten = p_DuurMinuten,
                    Opmerking = p_Opmerking,
                    DatumGewijzigd = NOW()
                WHERE Id = p_Id;

                -- Zet bestaande actieve koppelingen op inactief (soft-detach).
                UPDATE BehandelingPerProduct AS bpp
                INNER JOIN Behandeling AS b ON b.Id = bpp.BehandelingId
                SET bpp.IsActief = 0,
                    bpp.DatumGewijzigd = NOW()
                WHERE b.Id = p_Id
                  AND bpp.IsActief = 1;

                IF p_ProductId IS NOT NULL THEN
                    -- Voeg de nieuwe koppeling alleen toe als het product actief bestaat.
                    INSERT INTO BehandelingPerProduct (
                        BehandelingId, ProductId, Aantal, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd
                    )
                    SELECT p_Id, p.Id, 1, 1, NULL, NOW(), NOW()
                    FROM Product AS p
                    WHERE p.Id = p_ProductId
                      AND p.IsActief = 1;
                END IF;

                COMMIT;
            END
            SQL
        );
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS Sp_UpdateBehandeling');
    }
};
