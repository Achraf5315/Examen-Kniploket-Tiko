USE KniploketTiko;

DELIMITER $$
DROP PROCEDURE IF EXISTS Sp_DeleteBehandeling $$
CREATE PROCEDURE Sp_DeleteBehandeling(
    IN p_Id INT
)
BEGIN
    START TRANSACTION;

    UPDATE Behandeling
    SET
        IsActief = 0,
        DatumGewijzigd = NOW()
    WHERE Id = p_Id;

    UPDATE BehandelingPerProduct AS bpp
    INNER JOIN Behandeling AS b ON b.Id = bpp.BehandelingId
    SET
        bpp.IsActief = 0,
        bpp.DatumGewijzigd = NOW()
    WHERE b.Id = p_Id
      AND bpp.IsActief = 1;

    COMMIT;
END $$
DELIMITER ;
