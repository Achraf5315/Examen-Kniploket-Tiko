USE KniploketTiko;

DROP PROCEDURE IF EXISTS sp_CreateProduct //

DELIMITER //

CREATE PROCEDURE sp_CreateProduct (
    IN p_Productnaam VARCHAR(100),
    IN p_EanCode VARCHAR(20),
    IN p_CategorieId INT,
    IN p_Prijs DECIMAL(6,2),
    IN p_Voorraad SMALLINT,
    IN p_MinimumVoorraad SMALLINT,
    IN p_Opmerking VARCHAR(255),
    IN p_Leveranciers TEXT
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

    -- 1. VALIDATION: Productnaam
    IF p_Productnaam IS NULL OR p_Productnaam = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Productnaam verplicht';
    END IF;

    -- 2. VALIDATION: EAN-Code
    IF p_EanCode IS NULL OR p_EanCode NOT REGEXP '^[0-9]{8,14}$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'EAN ongeldig';
    END IF;

    -- 3. VALIDATION: Categorie exists
    SELECT COUNT(*) INTO v_categorie_exists
    FROM Categorie c
    WHERE c.Id = p_CategorieId
    AND c.IsActief = 1;

    IF v_categorie_exists = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Categorie bestaat niet of is niet actief';
    END IF;

    -- 4. VALIDATION: Prijs
    IF p_Prijs < 0.01 OR p_Prijs > 9999.99 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Prijs ongeldig';
    END IF;

    -- 5. VALIDATION: Voorraad
    IF p_Voorraad < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Voorraad ongeldig';
    END IF;

    -- 6. VALIDATION: MinimumVoorraad (only check negative, allow any value >= 0)
    IF p_MinimumVoorraad < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Minimumvoorraad ongeldig';
    END IF;

    -- 7. INSERT: Nieuw product
    INSERT INTO Product (
        Productnaam, EanCode, CategorieId, Prijs, Voorraad,
        MinimumVoorraad, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd
    )
    VALUES (
        p_Productnaam, p_EanCode, p_CategorieId, p_Prijs, p_Voorraad,
        p_MinimumVoorraad, 1, NULLIF(p_Opmerking, ''), NOW(), NOW()
    );

    SET v_product_id = LAST_INSERT_ID();

    -- 8. INSERT: Koppel leveranciers (3+ JOINS)
    IF p_Leveranciers IS NOT NULL AND p_Leveranciers <> '' THEN
        INSERT INTO ProductPerLeverancier (
            ProductId, LeverancierId, IsActief, DatumAangemaakt, DatumGewijzigd
        )
        SELECT DISTINCT
            p.Id AS ProductId,
            l.Id AS LeverancierId,
            1 AS IsActief,
            NOW() AS DatumAangemaakt,
            NOW() AS DatumGewijzigd
        FROM Product p
        JOIN Categorie c ON p.CategorieId = c.Id
        JOIN Leverancier l ON FIND_IN_SET(l.Id, p_Leveranciers) > 0
        JOIN (
            SELECT l2.Id, l2.Naam, l2.IsActief
            FROM Leverancier l2
            WHERE l2.IsActief = 1
        ) lv ON l.Id = lv.Id
        WHERE p.Id = v_product_id
        AND c.IsActief = 1
        AND l.IsActief = 1;
    END IF;

    COMMIT;

END //

DELIMITER ;