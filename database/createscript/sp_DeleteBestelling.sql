DROP PROCEDURE IF EXISTS sp_DeleteBestelling;

DELIMITER $$

CREATE PROCEDURE sp_DeleteBestelling(
    IN Id INT
)

BEGIN
    DELETE FROM Bestelling WHERE Id = Id;
END$$

DELIMITER ;

CALL sp_DeleteBestelling(1);