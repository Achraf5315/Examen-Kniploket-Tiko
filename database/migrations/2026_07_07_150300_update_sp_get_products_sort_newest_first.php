<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Wijzigt sp_GetProducts zodat het nieuwst toegevoegde product bovenaan staat.
     *
     * Was: ORDER BY p.Productnaam ASC (alfabetisch). Nu: ORDER BY p.Id DESC. De kolomsortering
     * die de gebruiker via de tabelkoppen kiest, wordt nog steeds in ProductController::index()
     * toegepast; dit is alleen de standaardvolgorde van de stored procedure.
     */
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetProducts');

        DB::unprepared("
            CREATE PROCEDURE sp_GetProducts(
                IN p_search       VARCHAR(255),
                IN p_categorieId  INT,
                IN p_lowStockOnly TINYINT
            )
            BEGIN
                SELECT
                    p.Id,
                    p.Productnaam,
                    p.EanCode,
                    p.Prijs,
                    p.Voorraad,
                    p.MinimumVoorraad,
                    p.IsActief,
                    p.Opmerking,
                    c.Id   AS categorie_id,
                    c.Naam AS categorie_naam,
                    GROUP_CONCAT(DISTINCT l.Naam ORDER BY l.Naam SEPARATOR ', ') AS leveranciers_namen
                FROM Product p
                INNER JOIN Categorie c
                    ON p.CategorieId = c.Id
                LEFT JOIN ProductPerLeverancier ppl
                    ON p.Id = ppl.ProductId
                    AND ppl.IsActief = 1
                LEFT JOIN Leverancier l
                    ON ppl.LeverancierId = l.Id
                    AND l.IsActief = 1
                WHERE p.IsActief = 1
                    AND (
                        p_search IS NULL
                        OR p_search = ''
                        OR p.Productnaam LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci
                        OR p.EanCode     LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci
                        OR p.Opmerking   LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci
                    )
                    AND (p_categorieId IS NULL OR p.CategorieId = p_categorieId)
                    AND (p_lowStockOnly = 0 OR p.Voorraad <= p.MinimumVoorraad)
                GROUP BY
                    p.Id, p.Productnaam, p.EanCode, p.Prijs,
                    p.Voorraad, p.MinimumVoorraad, p.IsActief,
                    p.Opmerking, c.Id, c.Naam
                ORDER BY p.Id DESC;
            END
        ");
    }

    /**
     * Zet de sortering terug naar alfabetisch op productnaam.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetProducts');

        DB::unprepared("
            CREATE PROCEDURE sp_GetProducts(
                IN p_search       VARCHAR(255),
                IN p_categorieId  INT,
                IN p_lowStockOnly TINYINT
            )
            BEGIN
                SELECT
                    p.Id,
                    p.Productnaam,
                    p.EanCode,
                    p.Prijs,
                    p.Voorraad,
                    p.MinimumVoorraad,
                    p.IsActief,
                    p.Opmerking,
                    c.Id   AS categorie_id,
                    c.Naam AS categorie_naam,
                    GROUP_CONCAT(DISTINCT l.Naam ORDER BY l.Naam SEPARATOR ', ') AS leveranciers_namen
                FROM Product p
                INNER JOIN Categorie c
                    ON p.CategorieId = c.Id
                LEFT JOIN ProductPerLeverancier ppl
                    ON p.Id = ppl.ProductId
                    AND ppl.IsActief = 1
                LEFT JOIN Leverancier l
                    ON ppl.LeverancierId = l.Id
                    AND l.IsActief = 1
                WHERE p.IsActief = 1
                    AND (
                        p_search IS NULL
                        OR p_search = ''
                        OR p.Productnaam LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci
                        OR p.EanCode     LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci
                        OR p.Opmerking   LIKE CONCAT('%', p_search, '%') COLLATE utf8mb4_unicode_ci
                    )
                    AND (p_categorieId IS NULL OR p.CategorieId = p_categorieId)
                    AND (p_lowStockOnly = 0 OR p.Voorraad <= p.MinimumVoorraad)
                GROUP BY
                    p.Id, p.Productnaam, p.EanCode, p.Prijs,
                    p.Voorraad, p.MinimumVoorraad, p.IsActief,
                    p.Opmerking, c.Id, c.Naam
                ORDER BY p.Productnaam ASC;
            END
        ");
    }
};
