USE KniploketTiko;

DROP PROCEDURE IF EXISTS sp_CreateBestelling;
DELIMITER $$

CREATE PROCEDURE sp_CreateBestelling(
    IN p_ProductId INT UNSIGNED,
    IN p_KlantId INT UNSIGNED,
    IN p_Orderdatum DATE,
    IN p_VerwachteLeverdatum DATE,
    IN p_Status VARCHAR(30)
)
BEGIN
    INSERT INTO Bestelling (
        ProductId,
        KlantId,
        Orderdatum,
        VerwachteLeverdatum,
        Status,
        IsActief,
        DatumAangemaakt,
        DatumGewijzigd
    ) VALUES (
        p_ProductId,
        p_KlantId,
        p_Orderdatum,
        p_VerwachteLeverdatum,
        p_Status,
        1,
        NOW(),
        NOW()
    );
END $$

DELIMITER ;