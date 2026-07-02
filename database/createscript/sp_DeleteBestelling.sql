SELECT * FROM Bestelling;

DROP PROCEDURE IF EXISTS sp_DeleteBestelling;

DELIMITER $$

CREATE PROCEDURE sp_DeleteBestelling(
    IN p_Id INT
)

BEGIN
    DELETE FROM Bestelling WHERE Id = p_Id;
END$$

DELIMITER ;

CALL sp_DeleteBestelling(1);