DROP PROCEDURE IF EXISTS sp_GetAllBestellingen;

DELIMITER $$

CREATE PROCEDURE sp_GetAllBestellingen()
BEGIN
    SELECT
        b.Id,
        b.ProductId,
        p.Productnaam AS ProductNaam,
        b.KlantId,
        k.Naam AS KlantNaam,    
        b.Orderdatum,
        b.VerwachteLeverdatum,
        b.Status,
        b.IsActief,
        b.Opmerking,
        b.DatumAangemaakt,
        b.DatumGewijzigd
    FROM Bestelling b
    LEFT JOIN Product p ON b.ProductId = p.Id
    LEFT JOIN Klant k ON b.KlantId = k.Id
    ORDER BY b.DatumGewijzigd DESC;
END $$

DELIMITER ;

CALL sp_GetAllBestellingen();