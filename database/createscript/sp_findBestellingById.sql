DROP PROCEDURE IF EXISTS sp_findBestellingById;

DELIMITER $$

CREATE PROCEDURE sp_findBestellingById(
    IN p_Id INT
)
BEGIN
    SELECT *
    FROM Bestelling
    WHERE Id = p_Id;
END$$

DELIMITER ;

CALL sp_findBestellingById(1);