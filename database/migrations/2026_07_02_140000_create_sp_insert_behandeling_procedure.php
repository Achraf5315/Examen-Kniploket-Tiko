<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Maakt de stored procedure Sp_InsertBehandeling aan.
     *
     * Deze migratie zorgt ervoor dat de procedure ook op de testdatabase bestaat
     * (RefreshDatabase draait alleen migraties). De losse SQL in database/createscript
     * is bedoeld voor handmatig opzetten; hier houden we hem herhaalbaar via migratie.
     */
    public function up(): void
    {
        // Eerst droppen zodat de migratie opnieuw uitgevoerd kan worden.
        DB::unprepared('DROP PROCEDURE IF EXISTS Sp_InsertBehandeling');

        DB::unprepared(
            <<<'SQL'
            CREATE PROCEDURE Sp_InsertBehandeling(
                IN p_Naam VARCHAR(100),
                IN p_Prijs DECIMAL(6, 2),
                IN p_DuurMinuten SMALLINT,
                IN p_Opmerking VARCHAR(255),
                IN p_ProductId INT
            )
            BEGIN
                DECLARE v_BehandelingId INT;

                -- Alles binnen één transactie zodat behandeling + koppeling samen slagen of falen.
                START TRANSACTION;

                INSERT INTO Behandeling (
                    Naam, Prijs, DuurMinuten, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd
                ) VALUES (
                    p_Naam, p_Prijs, p_DuurMinuten, 1, p_Opmerking, NOW(), NOW()
                );

                SET v_BehandelingId = LAST_INSERT_ID();

                IF p_ProductId IS NOT NULL THEN
                    -- Koppel alleen wanneer het product bestaat en actief is.
                    INSERT INTO BehandelingPerProduct (
                        BehandelingId, ProductId, Aantal, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd
                    )
                    SELECT v_BehandelingId, p.Id, 1, 1, NULL, NOW(), NOW()
                    FROM Product AS p
                    WHERE p.Id = p_ProductId
                      AND p.IsActief = 1;
                END IF;

                COMMIT;
            END
            SQL
        );
    }

    /**
     * Verwijdert de procedure bij rollback.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS Sp_InsertBehandeling');
    }
};
