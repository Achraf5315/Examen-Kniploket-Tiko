USE KniploketTiko;

-- Stored Procedure: overzicht van alle behandelingen inclusief gekoppelde productnamen
DELIMITER $$
DROP PROCEDURE IF EXISTS Sp_GetAllBehandelingen $$
CREATE PROCEDURE Sp_GetAllBehandelingen()
BEGIN
    /*
     * Haalt alle behandelingen op met gekoppelde producten.
     * LEFT JOIN zorgt ervoor dat ook behandelingen zonder producten zichtbaar blijven.
     */
    SELECT
        b.Id,
        b.Naam,
        b.Prijs,
        b.DuurMinuten,
        b.IsActief,
        b.Opmerking,
        GROUP_CONCAT(p.Productnaam SEPARATOR ', ') AS Producten
    FROM Behandeling AS b
    LEFT JOIN BehandelingPerProduct AS bpp ON bpp.BehandelingId = b.Id
        AND bpp.IsActief = 1
    LEFT JOIN Product AS p ON p.Id = bpp.ProductId
        AND p.IsActief = 1
    WHERE b.IsActief = 1
    GROUP BY
        b.Id,
        b.Naam,
        b.Prijs,
        b.DuurMinuten,
        b.IsActief,
        b.Opmerking
    ORDER BY b.Naam ASC;
END $$
DELIMITER ;
