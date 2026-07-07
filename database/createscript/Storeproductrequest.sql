USE KniploketTiko;

-- ==========================================
-- DROP OUDE PROCEDURES
-- ==========================================
DROP PROCEDURE IF EXISTS sp_GetProducts;
DROP PROCEDURE IF EXISTS sp_GetProductById;
DROP PROCEDURE IF EXISTS sp_UpdateProduct;
DROP PROCEDURE IF EXISTS sp_DeleteProduct;
DROP PROCEDURE IF EXISTS sp_CheckEanCode;

-- ==========================================
-- PROCEDURE 1: Haal alle producten op (3+ JOINS)
-- ==========================================
DELIMITER //

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
            OR p.Productnaam LIKE CONCAT('%', p_search, '%')
            OR p.EanCode     LIKE CONCAT('%', p_search, '%')
            OR p.Opmerking   LIKE CONCAT('%', p_search, '%')
        )
        AND (p_categorieId IS NULL OR p.CategorieId = p_categorieId)
        AND (p_lowStockOnly = 0 OR p.Voorraad <= p.MinimumVoorraad)
    GROUP BY
        p.Id, p.Productnaam, p.EanCode, p.Prijs,
        p.Voorraad, p.MinimumVoorraad, p.IsActief,
        p.Opmerking, c.Id, c.Naam
    ORDER BY p.Id DESC;
END//

DELIMITER ;

-- ==========================================
-- PROCEDURE 2: Haal één product op via ID (3+ JOINS)
-- ==========================================
DELIMITER //

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
        GROUP_CONCAT(DISTINCT l.Id   ORDER BY l.Id   SEPARATOR ',') AS leveranciers_ids,
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
    WHERE p.Id = p_id
        AND p.IsActief = 1
    GROUP BY
        p.Id, p.Productnaam, p.EanCode, p.Prijs,
        p.Voorraad, p.MinimumVoorraad, p.IsActief,
        p.Opmerking, p.CategorieId, p.DatumAangemaakt, p.DatumGewijzigd,
        c.Id, c.Naam
    LIMIT 1;
END//

DELIMITER ;

-- ==========================================
-- PROCEDURE 3: Bijwerken van een product
-- ==========================================
DELIMITER //

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
    IF p_Productnaam IS NULL OR p_Productnaam = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Productnaam is verplicht';
    END IF;

    -- Validatie: Prijs
    IF p_Prijs < 0.01 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Prijs moet groter zijn dan 0';
    END IF;

    -- Validatie: Voorraad
    IF p_Voorraad < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Voorraad kan niet negatief zijn';
    END IF;

    -- Validatie: Categorie bestaat
    SELECT COUNT(*) INTO v_categorie_exists
    FROM Categorie
    WHERE Id = p_CategorieId AND IsActief = 1;

    IF v_categorie_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Categorie bestaat niet of is niet actief';
    END IF;

    -- Update product
    UPDATE Product
    SET Productnaam     = p_Productnaam,
        EanCode         = p_EanCode,
        CategorieId     = p_CategorieId,
        Prijs           = p_Prijs,
        Voorraad        = p_Voorraad,
        MinimumVoorraad = p_MinimumVoorraad,
        Opmerking       = NULLIF(p_Opmerking, ''),
        DatumGewijzigd  = NOW()
    WHERE Id = p_id AND IsActief = 1;

    -- Verwijder bestaande leverancier koppelingen
    DELETE FROM ProductPerLeverancier
    WHERE ProductId = p_id;

    -- Voeg nieuwe leverancier koppelingen toe
    IF p_Leveranciers IS NOT NULL AND p_Leveranciers != '' THEN
        INSERT INTO ProductPerLeverancier (ProductId, LeverancierId, IsActief, DatumAangemaakt, DatumGewijzigd)
        SELECT p_id, l.Id, 1, NOW(), NOW()
        FROM Leverancier l
        WHERE FIND_IN_SET(l.Id, p_Leveranciers) > 0
          AND l.IsActief = 1;
    END IF;

    COMMIT;
END//

DELIMITER ;

-- ==========================================
-- PROCEDURE 4: Zacht verwijderen van een product
-- ==========================================
DELIMITER //

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

    -- Controleer of product gekoppeld is aan behandelingen
    SELECT COUNT(*) INTO v_heeft_behandelingen
    FROM BehandelingPerProduct
    WHERE ProductId = p_id AND IsActief = 1;

    IF v_heeft_behandelingen > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Product kan niet worden verwijderd omdat het gekoppeld is aan behandelingen';
    END IF;

    -- Verwijder leverancier koppelingen
    DELETE FROM ProductPerLeverancier
    WHERE ProductId = p_id;

    -- Soft delete (IsActief = 0)
    UPDATE Product
    SET IsActief       = 0,
        DatumGewijzigd = NOW()
    WHERE Id = p_id;

    COMMIT;
END//

DELIMITER ;

-- ==========================================
-- PROCEDURE 5: Controleer of EAN-code beschikbaar is
-- ==========================================
DELIMITER //

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
    WHERE EanCode = p_EanCode
      AND IsActief = 1
      AND (p_productId IS NULL OR Id != p_productId);
END//

DELIMITER ;

-- ==========================================
-- TEST QUERIES
-- ==========================================
-- CALL sp_GetProducts(NULL, NULL, 0);           -- Alle producten
-- CALL sp_GetProducts('Keune', NULL, 0);        -- Zoeken op naam
-- CALL sp_GetProducts(NULL, 1, 0);              -- Filter op categorie
-- CALL sp_GetProducts(NULL, NULL, 1);           -- Alleen lage voorraad
-- CALL sp_GetProductById(1);                    -- Enkel product
-- CALL sp_CheckEanCode('8717185223412', NULL);  -- EAN check