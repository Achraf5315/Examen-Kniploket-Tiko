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

    -- VALIDATION
    IF p_Productnaam IS NULL OR p_Productnaam = '' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Productnaam verplicht';
    END IF;

    IF p_EanCode IS NULL OR p_EanCode NOT REGEXP '^[0-9]{8,14}$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'EAN ongeldig';
    END IF;

    IF NOT EXISTS (SELECT 1 FROM Categorie WHERE Id = p_CategorieId) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Categorie bestaat niet';
    END IF;

    IF p_Prijs < 0.01 OR p_Prijs > 9999.99 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Prijs ongeldig';
    END IF;

    IF p_Voorraad < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Voorraad ongeldig';
    END IF;

    IF p_MinimumVoorraad < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Minimumvoorraad ongeldig';
    END IF;

    -- INSERT PRODUCT
    INSERT INTO Product (
        Productnaam, EanCode, CategorieId, Prijs, Voorraad,
        MinimumVoorraad, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd
    )
    VALUES (
        p_Productnaam, p_EanCode, p_CategorieId, p_Prijs, p_Voorraad,
        p_MinimumVoorraad, 1, p_Opmerking, NOW(), NOW()
    );

    SET v_product_id = LAST_INSERT_ID();

    -- KOPPEL LEVERANCIERS
    IF p_Leveranciers IS NOT NULL AND p_Leveranciers <> '' THEN
        INSERT INTO ProductPerLeverancier (
            ProductId, LeverancierId, IsActief, DatumAangemaakt, DatumGewijzigd
        )
        SELECT v_product_id, l.Id, 1, NOW(), NOW()
        FROM Leverancier l
        WHERE l.IsActief = 1
        AND FIND_IN_SET(l.Id, p_Leveranciers);
    END IF;

END //

DELIMITER ;