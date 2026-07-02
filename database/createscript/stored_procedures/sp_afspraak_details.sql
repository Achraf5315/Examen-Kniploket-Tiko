CREATE PROCEDURE spAfspraakDetails(
    IN p_Id INT UNSIGNED
)
BEGIN
    SELECT
        a.Id,
        a.KlantId,
        k.Naam AS KlantNaam,
        a.MedewerkerId,
        m.Naam AS MedewerkerNaam,
        a.BehandelingId,
        b.Naam AS BehandelingNaam,
        b.DuurMinuten,
        a.Datum,
        a.Starttijd,
        ADDTIME(a.Starttijd, SEC_TO_TIME(b.DuurMinuten * 60)) AS Eindtijd,
        a.Status,
        a.Opmerking
    FROM Afspraak a
    INNER JOIN Klant k ON k.Id = a.KlantId
    INNER JOIN Medewerker m ON m.Id = a.MedewerkerId
    INNER JOIN Behandeling b ON b.Id = a.BehandelingId
    WHERE a.Id = p_Id
      AND a.IsActief = 1;
END