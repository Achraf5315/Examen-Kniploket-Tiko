USE KniploketTiko;

DELIMITER $$
DROP PROCEDURE IF EXISTS Sp_InsertBehandeling $$
CREATE PROCEDURE Sp_InsertBehandeling(
    IN p_Naam VARCHAR(100),
    IN p_Prijs DECIMAL(6, 2),
    IN p_DuurMinuten SMALLINT,
    IN p_Opmerking VARCHAR(255),
    IN p_ProductId INT
)
BEGIN
    DECLARE v_BehandelingId INT;

    START TRANSACTION;

    INSERT INTO Behandeling (
        Naam,
        Prijs,
        DuurMinuten,
        IsActief,
        Opmerking,
        DatumAangemaakt,
        DatumGewijzigd
    ) VALUES (
        p_Naam,
        p_Prijs,
        p_DuurMinuten,
        1,
        p_Opmerking,
        NOW(),
        NOW()
    );

    SET v_BehandelingId = LAST_INSERT_ID();

    IF p_ProductId IS NOT NULL THEN
        /*
         * Voeg alleen een koppeling toe wanneer het product bestaat en actief is.
         * Dit gebeurt via INSERT ... SELECT op basis van Product.
         */
        INSERT INTO BehandelingPerProduct (
            BehandelingId,
            ProductId,
            Aantal,
            IsActief,
            Opmerking,
            DatumAangemaakt,
            DatumGewijzigd
        )
        SELECT
            v_BehandelingId,
            p.Id,
            1,
            1,
            NULL,
            NOW(),
            NOW()
        FROM Product AS p
        WHERE p.Id = p_ProductId
          AND p.IsActief = 1;
    END IF;

    COMMIT;
END $$
DELIMITER ;
