CREATE PROCEDURE spAfspraakVerwijderen(
    IN p_Id INT UNSIGNED
)
BEGIN
    DECLARE v_AantalGevonden INT DEFAULT 0;

    SELECT COUNT(*)
    INTO v_AantalGevonden
    FROM Afspraak a
    INNER JOIN Klant k ON k.Id = a.KlantId
    WHERE a.Id = p_Id;

    IF v_AantalGevonden = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'De afspraak is niet gevonden';
    END IF;

    DELETE a
    FROM Afspraak a
    INNER JOIN Klant k ON k.Id = a.KlantId
    WHERE a.Id = p_Id;
END