DROP PROCEDURE IF EXISTS sp_UpdateBestelling;

DELIMITER $$

CREATE PROCEDURE sp_UpdateBestelling(
    IN p_Id INT,
    IN p_KlantId INT,
    IN p_ProductId INT,
    IN p_Orderdatum DATE,
    IN p_VerwachteLeverdatum DATE,
    IN p_Status VARCHAR(30),
    IN p_Opmerking VARCHAR(255)
)
BEGIN
    UPDATE Bestelling
    SET
        KlantId = p_KlantId,
        ProductId = p_ProductId,
        Orderdatum = p_Orderdatum,
        VerwachteLeverdatum = p_VerwachteLeverdatum,
        Status = p_Status,
        DatumGewijzigd = NOW()
    WHERE Id = p_Id;
END$$

DELIMITER ;

CALL sp_UpdateBestelling(1, 2, 3, '2024-06-01', '2024-06-10', 'In behandeling', 'Opmerking bijwerken');