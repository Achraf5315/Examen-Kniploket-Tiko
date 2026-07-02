DROP DATABASE IF EXISTS KniploketTiko;
CREATE DATABASE KniploketTiko;
USE KniploketTiko;

-- 1. Gebruiker (Vereist voor Klant en Medewerker)
CREATE TABLE Gebruiker (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Email VARCHAR(255) NOT NULL UNIQUE,
    Wachtwoord VARCHAR(255) NOT NULL,
    IsActief BIT NOT NULL DEFAULT 1,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Rol
CREATE TABLE Rol (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Rolnaam VARCHAR(30) NOT NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. RolPerGebruiker
CREATE TABLE RolPerGebruiker (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    GebruikerId INT UNSIGNED NOT NULL,
    RolId INT UNSIGNED NOT NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL,
    FOREIGN KEY (GebruikerId) REFERENCES Gebruiker (Id),
    FOREIGN KEY (RolId) REFERENCES Rol (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Klant
CREATE TABLE Klant (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    GebruikerId INT UNSIGNED NOT NULL,
    Naam VARCHAR(150) NOT NULL,
    Telefoonnummer VARCHAR(20) NOT NULL,
    WensenAllergieen VARCHAR(100) NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL,
    FOREIGN KEY (GebruikerId) REFERENCES Gebruiker (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Adres
CREATE TABLE Adres (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    KlantId INT UNSIGNED NOT NULL,
    Straatnaam VARCHAR(100) NOT NULL,
    Huisnummer SMALLINT NOT NULL,
    Toevoeging VARCHAR(10) NULL,
    Postcode VARCHAR(10) NOT NULL,
    Plaats VARCHAR(100) NOT NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL,
    FOREIGN KEY (KlantId) REFERENCES Klant (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Categorie
CREATE TABLE Categorie (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(150) NOT NULL UNIQUE,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Leverancier
CREATE TABLE Leverancier (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(100) NOT NULL,
    Telefoonnummer VARCHAR(20) NOT NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Product
CREATE TABLE Product (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Productnaam VARCHAR(100) NOT NULL,
    EanCode VARCHAR(20) NOT NULL UNIQUE,
    CategorieId INT UNSIGNED NOT NULL,
    Prijs DECIMAL(6, 2) NOT NULL,
    Voorraad SMALLINT NOT NULL,
    MinimumVoorraad SMALLINT NOT NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL,
    FOREIGN KEY (CategorieId) REFERENCES Categorie (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. ProductPerLeverancier
CREATE TABLE ProductPerLeverancier (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ProductId INT UNSIGNED NOT NULL,
    LeverancierId INT UNSIGNED NOT NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL,
    FOREIGN KEY (ProductId) REFERENCES Product (Id),
    FOREIGN KEY (LeverancierId) REFERENCES Leverancier (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Behandeling
CREATE TABLE Behandeling (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(100) NOT NULL,
    Prijs DECIMAL(6, 2) NOT NULL,
    DuurMinuten SMALLINT NOT NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. BehandelingPerProduct
CREATE TABLE BehandelingPerProduct (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    BehandelingId INT UNSIGNED NOT NULL,
    ProductId INT UNSIGNED NOT NULL,
    Aantal SMALLINT NOT NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL,
    FOREIGN KEY (BehandelingId) REFERENCES Behandeling (Id),
    FOREIGN KEY (ProductId) REFERENCES Product (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Medewerker
CREATE TABLE Medewerker (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    GebruikerId INT UNSIGNED NOT NULL,
    AdresId INT UNSIGNED NOT NULL,
    Naam VARCHAR(100) NOT NULL,
    Telefoonnummer VARCHAR(20) NOT NULL,
    Specialisaties VARCHAR(50) NOT NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL,
    FOREIGN KEY (GebruikerId) REFERENCES Gebruiker (Id),
    FOREIGN KEY (AdresId) REFERENCES Adres (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. MedewerkerPerBehandeling
CREATE TABLE MedewerkerPerBehandeling (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    MedewerkerId INT UNSIGNED NOT NULL,
    BehandelingId INT UNSIGNED NOT NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL,
    FOREIGN KEY (MedewerkerId) REFERENCES Medewerker (Id),
    FOREIGN KEY (BehandelingId) REFERENCES Behandeling (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Werktijd
CREATE TABLE Werktijd (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    MedewerkerId INT UNSIGNED NOT NULL,
    Dag VARCHAR(10) NOT NULL,
    Starttijd TIME NOT NULL,
    Eindtijd TIME NOT NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL,
    FOREIGN KEY (MedewerkerId) REFERENCES Medewerker (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Afspraak
CREATE TABLE Afspraak (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    KlantId INT UNSIGNED NOT NULL,
    MedewerkerId INT UNSIGNED NOT NULL,
    BehandelingId INT UNSIGNED NOT NULL,
    Datum DATE NOT NULL,
    Starttijd TIME NOT NULL,
    Status VARCHAR(20) NOT NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL,
    FOREIGN KEY (KlantId) REFERENCES Klant (Id),
    FOREIGN KEY (MedewerkerId) REFERENCES Medewerker (Id),
    FOREIGN KEY (BehandelingId) REFERENCES Behandeling (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Bestelling
CREATE TABLE Bestelling (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ProductId INT UNSIGNED NOT NULL,
    KlantId INT UNSIGNED NOT NULL,
    Orderdatum DATE NOT NULL,
    VerwachteLeverdatum DATE NULL,
    Status VARCHAR(30) NOT NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL,
    FOREIGN KEY (ProductId) REFERENCES Product (Id),
    FOREIGN KEY (KlantId) REFERENCES Klant (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Bestelregel
CREATE TABLE Bestelregel (
    Id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    BestellingId INT UNSIGNED NOT NULL,
    Aantal INT NOT NULL,
    PrijsPerStuk DECIMAL(6, 2) NOT NULL,
    IsActief BIT NOT NULL,
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME NOT NULL,
    DatumGewijzigd DATETIME NOT NULL,
    FOREIGN KEY (BestellingId) REFERENCES Bestelling (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Gebruik de juiste database
USE KniploketTiko;

-- 1. Gebruiker data
INSERT INTO Gebruiker (Id, Email, Wachtwoord, IsActief, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 'admin@kniplokettiko.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW())
,(2, 'sanne.visser@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW())
,(3, 'dylan.bakker@hotmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW())
,(4, 'anouk.dejong@outlook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW())
,(5, 'bram.meijer@yahoo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW())
,(6, 'lisa.styling@kniplokettiko.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW())
,(7, 'tom.barber@kniplokettiko.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW());

-- 2. Klant data
INSERT INTO Klant (Id, GebruikerId, Naam, Telefoonnummer, WensenAllergieen, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 2, 'Sanne Visser', '0612345678', 'Allergisch voor parabenen', 1, 'Houdt van een rustige behandeling', NOW(), NOW())
,(2, 3, 'Dylan Bakker', '0623456789', NULL, 1, 'Komt vaak in het weekend', NOW(), NOW())
,(3, 4, 'Anouk de Jong', '0634567890', 'Gevoelige hoofdhuid', 1, NULL, NOW(), NOW())
,(4, 5, 'Bram Meijer', '0645678901', NULL, 1, 'Wil altijd kort aan de zijkanten', NOW(), NOW())
,(5, 1, 'Test Klant', '0600000000', NULL, 0, 'Inactief testaccount', NOW(), NOW());

-- 3. Adres data
INSERT INTO Adres (Id, KlantId, Straatnaam, Huisnummer, Toevoeging, Postcode, Plaats, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 1, 'Hoofdstraat', 14, 'A', '3511AA', 'Utrecht', 1, NULL, NOW(), NOW())
,(2, 2, 'Molenweg', 88, NULL, '1012AB', 'Amsterdam', 1, 'Slecht bereikbaar met auto', NOW(), NOW())
,(3, 3, 'Kerkplein', 3, NULL, '3011CC', 'Rotterdam', 1, NULL, NOW(), NOW())
,(4, 4, 'Stationsstraat', 112, 'B', '5611DD', 'Eindhoven', 1, NULL, NOW(), NOW())
,(5, 5, 'Dorpsstraat', 1, NULL, '7311EE', 'Apeldoorn', 1, NULL, NOW(), NOW());

-- 4. Categorie data
INSERT INTO Categorie (Id, Naam, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 'Shampoo & Conditioner', 1, 'Haarverzorgingsproducten', NOW(), NOW())
,(2, 'Styling & Gel', 1, 'Producten voor modellering', NOW(), NOW())
,(3, 'Haarkleuring', 1, 'Permanente en semi-permanente verf', NOW(), NOW())
,(4, 'Baardverzorging', 1, 'Oliën en balsems voor baarden', NOW(), NOW())
,(5, 'Tools & Borstels', 1, 'Kammen, borstels en föhns', NOW(), NOW());

-- 5. Leverancier data
INSERT INTO Leverancier (Id, Naam, Telefoonnummer, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 'LOréal Professional Nederland', '0201234567', 1, 'Vaste leverancier voor verf', NOW(), NOW())
,(2, 'Keune Haircosmetics', '0359876543', 1, 'Snelle levering', NOW(), NOW())
,(3, 'Wella Benelux', '0104445556', 1, NULL, NOW(), NOW())
,(4, 'The Alpha Men (Baard)', '0851112223', 1, 'Gespecialiseerd in herenproducten', NOW(), NOW())
,(5, 'Haarshop Groothandel', '0503332211', 1, 'Algemene kappersbenodigdheden', NOW(), NOW());

-- 6. Product data
INSERT INTO Product (Id, Productnaam, EanCode, CategorieId, Prijs, Voorraad, MinimumVoorraad, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 'Keune Care Absolute Volume Shampoo', '8717185223412', 1, 19.95, 25, 5, 1, 'Hardloper', NOW(), NOW())
,(2, 'LOréal Tecni.Art Clay Styling', '3474636971122', 2, 16.50, 15, 4, 1, NULL, NOW(), NOW())
,(3, 'Wella Color Touch 4/0', '4015600123456', 3, 12.95, 8, 3, 1, 'Kleur: Middenbruin', NOW(), NOW())
,(4, 'Primal Code Baardolie Premium', '7432109876543', 4, 24.95, 12, 2, 1, NULL, NOW(), NOW())
,(5, 'Kapperskam Antistatisch Zwart', '8711234567890', 5, 4.50, 40, 10, 1, 'Gebruik in de salon en verkoop', NOW(), NOW());

-- 7. ProductPerLeverancier data
INSERT INTO ProductPerLeverancier (Id, ProductId, LeverancierId, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 1, 2, 1, 'Rechtstreeks via Keune', NOW(), NOW())
,(2, 2, 1, 1, NULL, NOW(), NOW())
,(3, 3, 3, 1, NULL, NOW(), NOW())
,(4, 4, 4, 1, 'Exclusieve deal', NOW(), NOW())
,(5, 5, 5, 1, 'Goedkoopste optie', NOW(), NOW());

-- 8. Behandeling data
INSERT INTO Behandeling (Id, Naam, Prijs, DuurMinuten, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 'Wassen, Knippen & Drogen (Dames)', 42.50, 45, 1, 'Standaard damesbehandeling', NOW(), NOW())
,(2, 'Knippen & Stylen (Heren)', 29.50, 30, 1, 'Inclusief stylingproduct naar keuze', NOW(), NOW())
,(3, 'Haar Volledig Kleuren (Kort haar)', 65.00, 90, 1, 'Exclusief toeslag dik haar', NOW(), NOW())
,(4, 'Baard Trimmen & Contouren', 19.00, 20, 1, 'Met de trimmer en mes', NOW(), NOW())
,(5, 'Kinderen knippen (t/m 12 jaar)', 21.00, 20, 1, 'Zonder wassen', NOW(), NOW());

-- 9. BehandelingPerProduct data
INSERT INTO BehandelingPerProduct (Id, BehandelingId, ProductId, Aantal, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 1, 1, 1, 1, 'Gebruik van 1 dosering shampoo', NOW(), NOW())
;

-- 10. Rol data
INSERT INTO Rol (Id, RolNaam, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 'Admin', 1, 'Beheerder van het systeem', NOW(), NOW())
,(2, 'Medewerker', 1, 'Kapper/Stylist', NOW(), NOW())
,(3, 'Klant', 1, 'Reguliere klant', NOW(), NOW());

-- 11. RolPerGebruiker data
INSERT INTO RolPerGebruiker (Id, GebruikerId, RolId, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 1, 1, 1, 'Standaard admin', NOW(), NOW())
,(2, 2, 3, 1, NULL, NOW(), NOW())
,(3, 3, 3, 1, NULL, NOW(), NOW())
,(4, 4, 3, 1, NULL, NOW(), NOW())
,(5, 5, 3, 1, NULL, NOW(), NOW())
,(6, 6, 2, 1, 'Eigenares/Styling specialist', NOW(), NOW())
,(7, 7, 2, 1, 'Senior barber', NOW(), NOW());
,(2, 2, 2, 1, 1, 'Gebruik van een beetje klei/wax', NOW(), NOW())
,(3, 3, 3, 2, 1, 'Gemiddeld 2 tubes verf nodig', NOW(), NOW())
,(4, 4, 4, 1, 1, 'Paar druppels baardolie per afwerking', NOW(), NOW())
,(5, 1, 5, 1, 1, 'Kam gebruikt tijdens behandeling', NOW(), NOW());

-- 10. Medewerker data (Gekoppeld aan Gebruiker 6 en 7, en bestaande adressen)
INSERT INTO Medewerker (Id, GebruikerId, AdresId, Naam, Telefoonnummer, Specialisaties, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 6, 1, 'Lisa van der Meer', '0699887766', 'Kleuren, Bruidskapsels', 1, 'Eigenares', NOW(), NOW())
,(2, 7, 2, 'Tom Hendriks', '0655443322', 'Heren, Baarden, Opscheren', 1, 'Senior Barber', NOW(), NOW())
,(3, 1, 3, 'Flex Medewerker A', '0611112222', 'Allround', 1, 'Invalkracht', NOW(), NOW())
,(4, 1, 4, 'Flex Medewerker B', '0622223333', 'Wassen en Stylen', 1, 'Stagiair', NOW(), NOW())
,(5, 1, 5, 'Oude Medewerker', '0633334444', 'Geen', 0, 'Uit dienst', NOW(), NOW());

-- 11. MedewerkerPerBehandeling data
INSERT INTO MedewerkerPerBehandeling (Id, MedewerkerId, BehandelingId, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 1, 1, 1, 'Lisa doet alle dames', NOW(), NOW())
,(2, 1, 3, 1, 'Lisa is kleurspecialist', NOW(), NOW())
,(3, 2, 2, 1, 'Tom doet de heren', NOW(), NOW())
,(4, 2, 4, 1, 'Tom doet de baarden', NOW(), NOW())
,(5, 3, 5, 1, 'Invalkracht mag kinderen doen', NOW(), NOW());

-- 12. Werktijd data
INSERT INTO Werktijd (Id, MedewerkerId, Dag, Starttijd, Eindtijd, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 1, 'Dinsdag', '09:00:00', '18:00:00', 1, 'Vaste dag', NOW(), NOW())
,(2, 1, 'Donderdag', '09:00:00', '21:00:00', 1, 'Koopavond', NOW(), NOW())
,(3, 2, 'Woensdag', '09:00:00', '18:00:00', 1, NULL, NOW(), NOW())
,(4, 2, 'Zaterdag', '08:30:00', '16:00:00', 1, 'Drukke dag', NOW(), NOW())
,(5, 3, 'Vrijdag', '13:00:00', '18:00:00', 1, 'Alleen middag', NOW(), NOW());

-- 13. Afspraak data
INSERT INTO Afspraak (Id, KlantId, MedewerkerId, BehandelingId, Datum, Starttijd, Status, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 1, 1, 1, '2026-07-10', '10:00:00', 'Gereserveerd', 1, 'Wil graag koffie', NOW(), NOW())
,(2, 2, 2, 2, '2026-07-10', '11:30:00', 'Gereserveerd', 1, NULL, NOW(), NOW())
,(3, 3, 1, 3, '2026-07-11', '14:00:00', 'In afwachting', 1, 'Klant twijfelt over kleur', NOW(), NOW())
,(4, 4, 2, 4, '2026-07-12', '09:00:00', 'Afgerond', 1, 'Contant betaald', NOW(), NOW())
,(5, 1, 2, 2, '2026-06-01', '15:00:00', 'Geannuleerd', 1, 'Klant was ziek', NOW(), NOW());

-- 14. Bestelling data
INSERT INTO Bestelling (Id, ProductId, KlantId, Orderdatum, VerwachteLeverdatum, Status, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 1, 1, '2026-07-01', '2026-07-04', 'In behandeling', 1, 'Klant haalt het op in de salon', NOW(), NOW())
,(2, 3, 3, '2026-06-28', '2026-07-02', 'Verzonden', 1, NULL, NOW(), NOW())
,(3, 2, 2, '2026-06-15', '2026-06-18', 'Geleverd', 1, NULL, NOW(), NOW())
,(4, 4, 4, '2026-07-02', '2026-07-06', 'Nieuw', 1, 'Met spoed', NOW(), NOW())
,(5, 5, 1, '2026-05-10', '2026-05-14', 'Geleverd', 1, 'Kam voor thuisgebruik', NOW(), NOW());

-- 15. Bestelregel data
INSERT INTO Bestelregel (Id, BestellingId, Aantal, PrijsPerStuk, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd) VALUES
(1, 1, 2, 19.95, 1, 'Twee flessen shampoo', NOW(), NOW())
,(2, 2, 4, 12.95, 1, 'Voorraad voor kleuring', NOW(), NOW())
,(3, 3, 1, 16.50, 1, NULL, NOW(), NOW())
,(4, 4, 1, 24.95, 1, NULL, NOW(), NOW())
,(5, 5, 3, 4.50, 1, 'Extra kammen', NOW(), NOW());