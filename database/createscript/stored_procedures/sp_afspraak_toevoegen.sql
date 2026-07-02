CREATE PROCEDURE spAfspraakToevoegen(
    IN p_KlantId INT UNSIGNED,
    IN p_MedewerkerId INT UNSIGNED,
    IN p_BehandelingId INT UNSIGNED,
    IN p_Datum DATE,
    IN p_Starttijd TIME
)
BEGIN
    DECLARE v_AantalOverlappend INT DEFAULT 0;
    DECLARE v_NieuweEindtijd TIME;

    SELECT ADDTIME(p_Starttijd, SEC_TO_TIME(b.DuurMinuten * 60))
    INTO v_NieuweEindtijd
    FROM Behandeling b
    WHERE b.Id = p_BehandelingId;

    IF p_Datum < CURDATE() THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'De afspraak kan niet in het verleden worden gepland';
    END IF;

    IF p_Datum = CURDATE() AND p_Starttijd < CURTIME() THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'De starttijd ligt in het verleden';
    END IF;

    SELECT COUNT(*)
    INTO v_AantalOverlappend
    FROM Afspraak a
    INNER JOIN Behandeling b ON b.Id = a.BehandelingId
    WHERE a.MedewerkerId = p_MedewerkerId
      AND a.Datum = p_Datum
      AND a.IsActief = 1
      AND a.Status <> 'Geannuleerd'
      AND p_Starttijd < ADDTIME(a.Starttijd, SEC_TO_TIME(b.DuurMinuten * 60))
      AND v_NieuweEindtijd > a.Starttijd;

    IF v_AantalOverlappend > 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'De afspraak overlapt met een bestaande afspraak';
    END IF;

    INSERT INTO Afspraak
        (KlantId, MedewerkerId, BehandelingId, Datum, Starttijd, Status, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
    VALUES
        (p_KlantId, p_MedewerkerId, p_BehandelingId, p_Datum, p_Starttijd, 'Gereserveerd', 1, NULL, NOW(), NOW());

    SELECT LAST_INSERT_ID() AS Id;
END