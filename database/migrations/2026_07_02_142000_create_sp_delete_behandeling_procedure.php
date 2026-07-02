<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Maakt de stored procedure Sp_DeleteBehandeling aan.
     *
     * Dit is een soft-delete: de behandeling en haar productkoppelingen worden op
     * IsActief = 0 gezet, zodat historische afspraken blijven verwijzen (integriteit).
     */
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS Sp_DeleteBehandeling');

        DB::unprepared(
            <<<'SQL'
            CREATE PROCEDURE Sp_DeleteBehandeling(
                IN p_Id INT
            )
            BEGIN
                START TRANSACTION;

                -- Behandeling zelf op inactief zetten.
                UPDATE Behandeling
                SET IsActief = 0,
                    DatumGewijzigd = NOW()
                WHERE Id = p_Id;

                -- Bijbehorende productkoppelingen ook op inactief zetten.
                UPDATE BehandelingPerProduct AS bpp
                INNER JOIN Behandeling AS b ON b.Id = bpp.BehandelingId
                SET bpp.IsActief = 0,
                    bpp.DatumGewijzigd = NOW()
                WHERE b.Id = p_Id
                  AND bpp.IsActief = 1;

                COMMIT;
            END
            SQL
        );
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS Sp_DeleteBehandeling');
    }
};
