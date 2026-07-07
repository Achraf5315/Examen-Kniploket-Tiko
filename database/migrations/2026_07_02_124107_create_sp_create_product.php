<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates 5 stored procedures for product management:
     * - sp_GetProducts: Get all products with filters (search, category, low stock)
     * - sp_GetProductById: Get single product with all details
     * - sp_CreateProduct: Create new product with suppliers
     * - sp_UpdateProduct: Update product with validation and transactions
     * - sp_DeleteProduct: Soft delete product with business logic checks
     */
    public function up(): void
    {
        // Drop existing procedures for idempotency
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetProducts');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetProductById');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_CreateProduct');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_UpdateProduct');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_DeleteProduct');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_CheckEanCode');

        // ==========================================
        // PROCEDURE 1: Get all products with filters
        // ==========================================
        DB::unprepared('
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
                    GROUP_CONCAT(DISTINCT l.Naam ORDER BY l.Naam SEPARATOR \', \') AS leveranciers_namen
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
                        OR p_search = \'\'
                        OR p.Productnaam LIKE CONCAT(\'%\', p_search, \'%\') COLLATE utf8mb4_unicode_ci
                        OR p.EanCode     LIKE CONCAT(\'%\', p_search, \'%\') COLLATE utf8mb4_unicode_ci
                        OR p.Opmerking   LIKE CONCAT(\'%\', p_search, \'%\') COLLATE utf8mb4_unicode_ci
                    )
                    AND (p_categorieId IS NULL OR p.CategorieId = p_categorieId)
                    AND (p_lowStockOnly = 0 OR p.Voorraad <= p.MinimumVoorraad)
                GROUP BY
                    p.Id, p.Productnaam, p.EanCode, p.Prijs,
                    p.Voorraad, p.MinimumVoorraad, p.IsActief,
                    p.Opmerking, c.Id, c.Naam
                ORDER BY p.Productnaam ASC;
            END
        ');

        // ==========================================
        // PROCEDURE 2: Get single product by ID
        // ==========================================
        DB::unprepared('
            CREATE PROCEDURE sp_GetProductById(
                IN p_id INT
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
                    p.CategorieId,
                    p.DatumAangemaakt,
                    p.DatumGewijzigd,
                    c.Id   AS categorie_id,
                    c.Naam AS categorie_naam,
                    GROUP_CONCAT(DISTINCT l.Id   ORDER BY l.Id   SEPARATOR \',\') AS leveranciers_ids,
                    GROUP_CONCAT(DISTINCT l.Naam ORDER BY l.Naam SEPARATOR \', \') AS leveranciers_namen
                FROM Product p
                INNER JOIN Categorie c
                    ON p.CategorieId = c.Id
                LEFT JOIN ProductPerLeverancier ppl
                    ON p.Id = ppl.ProductId
                    AND ppl.IsActief = 1
                LEFT JOIN Leverancier l
                    ON ppl.LeverancierId = l.Id
                    AND l.IsActief = 1
                WHERE p.Id = p_id
                    AND p.IsActief = 1
                GROUP BY
                    p.Id, p.Productnaam, p.EanCode, p.Prijs,
                    p.Voorraad, p.MinimumVoorraad, p.IsActief,
                    p.Opmerking, p.CategorieId, p.DatumAangemaakt, p.DatumGewijzigd,
                    c.Id, c.Naam
                LIMIT 1;
            END
        ');

        // ==========================================
        // PROCEDURE 3: Create new product
        // ==========================================
        DB::unprepared('
            CREATE PROCEDURE sp_CreateProduct(
                IN p_Productnaam     VARCHAR(100),
                IN p_EanCode         VARCHAR(20),
                IN p_CategorieId     INT,
                IN p_Prijs           DECIMAL(6,2),
                IN p_Voorraad        SMALLINT,
                IN p_MinimumVoorraad SMALLINT,
                IN p_Opmerking       VARCHAR(255),
                IN p_Leveranciers    TEXT
            )
            BEGIN
                DECLARE v_product_id INT;
                DECLARE v_categorie_exists INT DEFAULT 0;

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;

                -- Validatie: Productnaam
                IF p_Productnaam IS NULL OR p_Productnaam = \'\' THEN
                    SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'Productnaam is verplicht\';
                END IF;

                -- Validatie: EAN-code uniek
                IF EXISTS (SELECT 1 FROM Product WHERE EanCode = p_EanCode COLLATE utf8mb4_unicode_ci AND IsActief = 1) THEN
                    SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'EAN-code bestaat al\';
                END IF;

                -- Validatie: Prijs
                IF p_Prijs < 0.01 THEN
                    SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'Prijs moet groter zijn dan 0\';
                END IF;

                -- Validatie: Voorraad
                IF p_Voorraad < 0 THEN
                    SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'Voorraad kan niet negatief zijn\';
                END IF;

                -- Validatie: Categorie bestaat
                SELECT COUNT(*) INTO v_categorie_exists
                FROM Categorie
                WHERE Id = p_CategorieId AND IsActief = 1;

                IF v_categorie_exists = 0 THEN
                    SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'Categorie bestaat niet of is niet actief\';
                END IF;

                -- Insert product
                INSERT INTO Product (
                    Productnaam,
                    EanCode,
                    CategorieId,
                    Prijs,
                    Voorraad,
                    MinimumVoorraad,
                    Opmerking,
                    IsActief,
                    DatumAangemaakt,
                    DatumGewijzigd
                )
                VALUES (
                    p_Productnaam,
                    p_EanCode,
                    p_CategorieId,
                    p_Prijs,
                    p_Voorraad,
                    p_MinimumVoorraad,
                    NULLIF(p_Opmerking, \'\'),
                    1,
                    NOW(),
                    NOW()
                );

                SET v_product_id = LAST_INSERT_ID();

                -- Add suppliers
                IF p_Leveranciers IS NOT NULL AND p_Leveranciers != \'\' THEN
                    INSERT INTO ProductPerLeverancier (ProductId, LeverancierId, IsActief, DatumAangemaakt, DatumGewijzigd)
                    SELECT v_product_id, l.Id, 1, NOW(), NOW()
                    FROM Leverancier l
                    WHERE FIND_IN_SET(l.Id, p_Leveranciers) > 0
                      AND l.IsActief = 1;
                END IF;

                COMMIT;
            END
        ');

        // ==========================================
        // PROCEDURE 4: Update product
        // ==========================================
        DB::unprepared('
            CREATE PROCEDURE sp_UpdateProduct(
                IN p_id              INT,
                IN p_Productnaam     VARCHAR(100),
                IN p_EanCode         VARCHAR(20),
                IN p_CategorieId     INT,
                IN p_Prijs           DECIMAL(6,2),
                IN p_Voorraad        SMALLINT,
                IN p_MinimumVoorraad SMALLINT,
                IN p_Opmerking       VARCHAR(255),
                IN p_Leveranciers    TEXT
            )
            BEGIN
                DECLARE v_categorie_exists INT DEFAULT 0;

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;

                -- Validatie: Productnaam
                IF p_Productnaam IS NULL OR p_Productnaam = \'\' THEN
                    SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'Productnaam is verplicht\';
                END IF;

                -- Validatie: Prijs
                IF p_Prijs < 0.01 THEN
                    SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'Prijs moet groter zijn dan 0\';
                END IF;

                -- Validatie: Voorraad
                IF p_Voorraad < 0 THEN
                    SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'Voorraad kan niet negatief zijn\';
                END IF;

                -- Validatie: Categorie bestaat
                SELECT COUNT(*) INTO v_categorie_exists
                FROM Categorie
                WHERE Id = p_CategorieId AND IsActief = 1;

                IF v_categorie_exists = 0 THEN
                    SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'Categorie bestaat niet of is niet actief\';
                END IF;

                -- Update product
                UPDATE Product
                SET Productnaam     = p_Productnaam,
                    EanCode         = p_EanCode,
                    CategorieId     = p_CategorieId,
                    Prijs           = p_Prijs,
                    Voorraad        = p_Voorraad,
                    MinimumVoorraad = p_MinimumVoorraad,
                    Opmerking       = NULLIF(p_Opmerking, \'\'),
                    DatumGewijzigd  = NOW()
                WHERE Id = p_id AND IsActief = 1;

                -- Delete existing supplier links
                DELETE FROM ProductPerLeverancier
                WHERE ProductId = p_id;

                -- Add new supplier links
                IF p_Leveranciers IS NOT NULL AND p_Leveranciers != \'\' THEN
                    INSERT INTO ProductPerLeverancier (ProductId, LeverancierId, IsActief, DatumAangemaakt, DatumGewijzigd)
                    SELECT p_id, l.Id, 1, NOW(), NOW()
                    FROM Leverancier l
                    WHERE FIND_IN_SET(l.Id, p_Leveranciers) > 0
                      AND l.IsActief = 1;
                END IF;

                COMMIT;
            END
        ');

        // ==========================================
        // PROCEDURE 5: Delete product (soft delete)
        // ==========================================
        DB::unprepared('
            CREATE PROCEDURE sp_DeleteProduct(
                IN p_id INT
            )
            BEGIN
                DECLARE v_heeft_behandelingen INT DEFAULT 0;

                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;

                -- Check if product is linked to treatments
                SELECT COUNT(*) INTO v_heeft_behandelingen
                FROM BehandelingPerProduct
                WHERE ProductId = p_id AND IsActief = 1;

                IF v_heeft_behandelingen > 0 THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'Product kan niet worden verwijderd omdat het gekoppeld is aan behandelingen\';
                END IF;

                -- Delete supplier links
                DELETE FROM ProductPerLeverancier
                WHERE ProductId = p_id;

                -- Soft delete
                UPDATE Product
                SET IsActief       = 0,
                    DatumGewijzigd = NOW()
                WHERE Id = p_id;

                COMMIT;
            END
        ');

        // ==========================================
        // PROCEDURE 6: Check EAN code availability
        // ==========================================
        DB::unprepared('
            CREATE PROCEDURE sp_CheckEanCode(
                IN p_EanCode   VARCHAR(20),
                IN p_productId INT
            )
            BEGIN
                SELECT
                    CASE
                        WHEN COUNT(*) = 0 THEN 1
                        ELSE 0
                    END AS is_available
                FROM Product
                WHERE EanCode = p_EanCode COLLATE utf8mb4_unicode_ci
                  AND IsActief = 1
                  AND (p_productId IS NULL OR Id != p_productId);
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetProducts');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_GetProductById');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_CreateProduct');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_UpdateProduct');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_DeleteProduct');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_CheckEanCode');
    }
};