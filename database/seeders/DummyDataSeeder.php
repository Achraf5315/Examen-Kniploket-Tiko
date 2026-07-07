<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert data
        DB::table('Gebruiker')->insert([
            ['Id' => 2, 'Email' => 'sanne.visser@gmail.com', 'Gebruikersnaam' => 'sanne.visser', 'Wachtwoord' => 'sanne123', 'IsActief' => 1, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'Email' => 'dylan.bakker@hotmail.com', 'Gebruikersnaam' => 'dylan.bakker', 'Wachtwoord' => 'dylan123', 'IsActief' => 1, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'Email' => 'anouk.dejong@outlook.com', 'Gebruikersnaam' => 'anouk.dejong', 'Wachtwoord' => 'anouk123', 'IsActief' => 1, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'Email' => 'bram.meijer@yahoo.com', 'Gebruikersnaam' => 'bram.meijer', 'Wachtwoord' => 'bram123', 'IsActief' => 1, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 6, 'Email' => 'lisa.styling@kniplokettiko.nl', 'Gebruikersnaam' => 'lisa.styling', 'Wachtwoord' => 'lisa123', 'IsActief' => 1, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 7, 'Email' => 'tom.barber@kniplokettiko.nl', 'Gebruikersnaam' => 'tom.barber', 'Wachtwoord' => 'tom123', 'IsActief' => 1, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        // De Admin-rol en de koppeling van de eigenaar (GebruikerId 1) daaraan
        // worden al aangemaakt door DatabaseSeeder, dus die slaan we hier over
        // om dubbele primary keys te voorkomen.
        DB::table('Rol')->insert([
            ['Id' => 2, 'Rolnaam' => 'Medewerker', 'IsActief' => 1, 'Opmerking' => 'Kapper/Stylist', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'Rolnaam' => 'Klant', 'IsActief' => 1, 'Opmerking' => 'Reguliere klant', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('RolPerGebruiker')->insert([
            ['GebruikerId' => 2, 'RolId' => 3, 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['GebruikerId' => 3, 'RolId' => 3, 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['GebruikerId' => 4, 'RolId' => 3, 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['GebruikerId' => 5, 'RolId' => 3, 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['GebruikerId' => 6, 'RolId' => 2, 'IsActief' => 1, 'Opmerking' => 'Eigenares/Styling specialist', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['GebruikerId' => 7, 'RolId' => 2, 'IsActief' => 1, 'Opmerking' => 'Senior barber', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('Klant')->insert([
            ['Id' => 1, 'GebruikerId' => 2, 'Naam' => 'Sanne Visser', 'Telefoonnummer' => '0612345678', 'WensenAllergieen' => 'Allergisch voor parabenen', 'IsActief' => 1, 'Opmerking' => 'Houdt van een rustige behandeling', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'GebruikerId' => 3, 'Naam' => 'Dylan Bakker', 'Telefoonnummer' => '0623456789', 'WensenAllergieen' => null, 'IsActief' => 1, 'Opmerking' => 'Komt vaak in het weekend', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'GebruikerId' => 4, 'Naam' => 'Anouk de Jong', 'Telefoonnummer' => '0634567890', 'WensenAllergieen' => 'Gevoelige hoofdhuid', 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'GebruikerId' => 5, 'Naam' => 'Bram Meijer', 'Telefoonnummer' => '0645678901', 'WensenAllergieen' => null, 'IsActief' => 1, 'Opmerking' => 'Wil altijd kort aan de zijkanten', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'GebruikerId' => 1, 'Naam' => 'Test Klant', 'Telefoonnummer' => '0600000000', 'WensenAllergieen' => null, 'IsActief' => 0, 'Opmerking' => 'Inactief testaccount', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('Adres')->insert([
            ['Id' => 1, 'KlantId' => 1, 'Straatnaam' => 'Hoofdstraat', 'Huisnummer' => 14, 'Toevoeging' => 'A', 'Postcode' => '3511AA', 'Plaats' => 'Utrecht', 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'KlantId' => 2, 'Straatnaam' => 'Molenweg', 'Huisnummer' => 88, 'Toevoeging' => null, 'Postcode' => '1012AB', 'Plaats' => 'Amsterdam', 'IsActief' => 1, 'Opmerking' => 'Slecht bereikbaar met auto', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'KlantId' => 3, 'Straatnaam' => 'Kerkplein', 'Huisnummer' => 3, 'Toevoeging' => null, 'Postcode' => '3011CC', 'Plaats' => 'Rotterdam', 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'KlantId' => 4, 'Straatnaam' => 'Stationsstraat', 'Huisnummer' => 112, 'Toevoeging' => 'B', 'Postcode' => '5611DD', 'Plaats' => 'Eindhoven', 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'KlantId' => 5, 'Straatnaam' => 'Dorpsstraat', 'Huisnummer' => 1, 'Toevoeging' => null, 'Postcode' => '7311EE', 'Plaats' => 'Apeldoorn', 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('Categorie')->insert([
            ['Id' => 1, 'Naam' => 'Shampoo & Conditioner', 'IsActief' => 1, 'Opmerking' => 'Haarverzorgingsproducten', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'Naam' => 'Styling & Gel', 'IsActief' => 1, 'Opmerking' => 'Producten voor modellering', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'Naam' => 'Haarkleuring', 'IsActief' => 1, 'Opmerking' => 'Permanente en semi-permanente verf', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'Naam' => 'Baardverzorging', 'IsActief' => 1, 'Opmerking' => 'Oliën en balsems voor baarden', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'Naam' => 'Tools & Borstels', 'IsActief' => 1, 'Opmerking' => 'Kammen, borstels en föhns', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('Leverancier')->insert([
            ['Id' => 1, 'Naam' => 'LOréal Professional Nederland', 'Telefoonnummer' => '0201234567', 'IsActief' => 1, 'Opmerking' => 'Vaste leverancier voor verf', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'Naam' => 'Keune Haircosmetics', 'Telefoonnummer' => '0359876543', 'IsActief' => 1, 'Opmerking' => 'Snelle levering', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'Naam' => 'Wella Benelux', 'Telefoonnummer' => '0104445556', 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'Naam' => 'The Alpha Men (Baard)', 'Telefoonnummer' => '0851112223', 'IsActief' => 1, 'Opmerking' => 'Gespecialiseerd in herenproducten', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'Naam' => 'Haarshop Groothandel', 'Telefoonnummer' => '0503332211', 'IsActief' => 1, 'Opmerking' => 'Algemene kappersbenodigdheden', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('Product')->insert([
            ['Id' => 1, 'Productnaam' => 'Keune Care Absolute Volume Shampoo', 'EanCode' => '8717185223412', 'CategorieId' => 1, 'Prijs' => 19.95, 'Voorraad' => 25, 'MinimumVoorraad' => 5, 'IsActief' => 1, 'Opmerking' => 'Hardloper', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'Productnaam' => 'LOréal Tecni.Art Clay Styling', 'EanCode' => '3474636971122', 'CategorieId' => 2, 'Prijs' => 16.50, 'Voorraad' => 15, 'MinimumVoorraad' => 4, 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'Productnaam' => 'Wella Color Touch 4/0', 'EanCode' => '4015600123456', 'CategorieId' => 3, 'Prijs' => 12.95, 'Voorraad' => 8, 'MinimumVoorraad' => 3, 'IsActief' => 1, 'Opmerking' => 'Kleur: Middenbruin', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'Productnaam' => 'Primal Code Baardolie Premium', 'EanCode' => '7432109876543', 'CategorieId' => 4, 'Prijs' => 24.95, 'Voorraad' => 12, 'MinimumVoorraad' => 2, 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'Productnaam' => 'Kapperskam Antistatisch Zwart', 'EanCode' => '8711234567890', 'CategorieId' => 5, 'Prijs' => 4.50, 'Voorraad' => 40, 'MinimumVoorraad' => 10, 'IsActief' => 1, 'Opmerking' => 'Gebruik in de salon en verkoop', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('ProductPerLeverancier')->insert([
            ['Id' => 1, 'ProductId' => 1, 'LeverancierId' => 2, 'IsActief' => 1, 'Opmerking' => 'Rechtstreeks via Keune', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'ProductId' => 2, 'LeverancierId' => 1, 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'ProductId' => 3, 'LeverancierId' => 3, 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'ProductId' => 4, 'LeverancierId' => 4, 'IsActief' => 1, 'Opmerking' => 'Exclusieve deal', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'ProductId' => 5, 'LeverancierId' => 5, 'IsActief' => 1, 'Opmerking' => 'Goedkoopste optie', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('Behandeling')->insert([
            ['Id' => 1, 'Naam' => 'Wassen, Knippen & Drogen (Dames)', 'Prijs' => 42.50, 'DuurMinuten' => 45, 'IsActief' => 1, 'Opmerking' => 'Standaard damesbehandeling', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'Naam' => 'Knippen & Stylen (Heren)', 'Prijs' => 29.50, 'DuurMinuten' => 30, 'IsActief' => 1, 'Opmerking' => 'Inclusief stylingproduct naar keuze', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'Naam' => 'Haar Volledig Kleuren (Kort haar)', 'Prijs' => 65.00, 'DuurMinuten' => 90, 'IsActief' => 1, 'Opmerking' => 'Exclusief toeslag dik haar', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'Naam' => 'Baard Trimmen & Contouren', 'Prijs' => 19.00, 'DuurMinuten' => 20, 'IsActief' => 1, 'Opmerking' => 'Met de trimmer en mes', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'Naam' => 'Kinderen knippen (t/m 12 jaar)', 'Prijs' => 21.00, 'DuurMinuten' => 20, 'IsActief' => 1, 'Opmerking' => 'Zonder wassen', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('BehandelingPerProduct')->insert([
            ['Id' => 1, 'BehandelingId' => 1, 'ProductId' => 1, 'Aantal' => 1, 'IsActief' => 1, 'Opmerking' => 'Gebruik van 1 dosering shampoo', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'BehandelingId' => 2, 'ProductId' => 2, 'Aantal' => 1, 'IsActief' => 1, 'Opmerking' => 'Gebruik van een beetje klei/wax', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'BehandelingId' => 3, 'ProductId' => 3, 'Aantal' => 2, 'IsActief' => 1, 'Opmerking' => 'Gemiddeld 2 tubes verf nodig', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'BehandelingId' => 4, 'ProductId' => 4, 'Aantal' => 1, 'IsActief' => 1, 'Opmerking' => 'Paar druppels baardolie per afwerking', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'BehandelingId' => 1, 'ProductId' => 5, 'Aantal' => 1, 'IsActief' => 1, 'Opmerking' => 'Kam gebruikt tijdens behandeling', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('Medewerker')->insert([
            ['Id' => 1, 'GebruikerId' => 6, 'AdresId' => 1, 'Naam' => 'Lisa van der Meer', 'Telefoonnummer' => '0699887766', 'Specialisaties' => 'Kleuren, Bruidskapsels', 'IsActief' => 1, 'Opmerking' => 'Eigenares', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'GebruikerId' => 7, 'AdresId' => 2, 'Naam' => 'Tom Hendriks', 'Telefoonnummer' => '0655443322', 'Specialisaties' => 'Heren, Baarden, Opscheren', 'IsActief' => 1, 'Opmerking' => 'Senior Barber', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'GebruikerId' => 1, 'AdresId' => 3, 'Naam' => 'Flex Medewerker A', 'Telefoonnummer' => '0611112222', 'Specialisaties' => 'Allround', 'IsActief' => 1, 'Opmerking' => 'Invalkracht', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'GebruikerId' => 1, 'AdresId' => 4, 'Naam' => 'Flex Medewerker B', 'Telefoonnummer' => '0622223333', 'Specialisaties' => 'Wassen en Stylen', 'IsActief' => 1, 'Opmerking' => 'Stagiair', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'GebruikerId' => 1, 'AdresId' => 5, 'Naam' => 'Oude Medewerker', 'Telefoonnummer' => '0633334444', 'Specialisaties' => 'Geen', 'IsActief' => 0, 'Opmerking' => 'Uit dienst', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('MedewerkerPerBehandeling')->insert([
            ['Id' => 1, 'MedewerkerId' => 1, 'BehandelingId' => 1, 'IsActief' => 1, 'Opmerking' => 'Lisa doet alle dames', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'MedewerkerId' => 1, 'BehandelingId' => 3, 'IsActief' => 1, 'Opmerking' => 'Lisa is kleurspecialist', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'MedewerkerId' => 2, 'BehandelingId' => 2, 'IsActief' => 1, 'Opmerking' => 'Tom doet de heren', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'MedewerkerId' => 2, 'BehandelingId' => 4, 'IsActief' => 1, 'Opmerking' => 'Tom doet de baarden', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'MedewerkerId' => 3, 'BehandelingId' => 5, 'IsActief' => 1, 'Opmerking' => 'Invalkracht mag kinderen doen', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('Werktijd')->insert([
            ['Id' => 1, 'MedewerkerId' => 1, 'Dag' => 'Dinsdag', 'Starttijd' => '09:00:00', 'Eindtijd' => '18:00:00', 'IsActief' => 1, 'Opmerking' => 'Vaste dag', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'MedewerkerId' => 1, 'Dag' => 'Donderdag', 'Starttijd' => '09:00:00', 'Eindtijd' => '21:00:00', 'IsActief' => 1, 'Opmerking' => 'Koopavond', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'MedewerkerId' => 2, 'Dag' => 'Woensdag', 'Starttijd' => '09:00:00', 'Eindtijd' => '18:00:00', 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'MedewerkerId' => 2, 'Dag' => 'Zaterdag', 'Starttijd' => '08:30:00', 'Eindtijd' => '16:00:00', 'IsActief' => 1, 'Opmerking' => 'Drukke dag', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'MedewerkerId' => 3, 'Dag' => 'Vrijdag', 'Starttijd' => '13:00:00', 'Eindtijd' => '18:00:00', 'IsActief' => 1, 'Opmerking' => 'Alleen middag', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('Afspraak')->insert([
            ['Id' => 1, 'KlantId' => 1, 'MedewerkerId' => 1, 'BehandelingId' => 1, 'Datum' => '2026-07-10', 'Starttijd' => '10:00:00', 'Status' => 'Gereserveerd', 'IsActief' => 1, 'Opmerking' => 'Wil graag koffie', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'KlantId' => 2, 'MedewerkerId' => 2, 'BehandelingId' => 2, 'Datum' => '2026-07-10', 'Starttijd' => '11:30:00', 'Status' => 'Gereserveerd', 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'KlantId' => 3, 'MedewerkerId' => 1, 'BehandelingId' => 3, 'Datum' => '2026-07-11', 'Starttijd' => '14:00:00', 'Status' => 'In afwachting', 'IsActief' => 1, 'Opmerking' => 'Klant twijfelt over kleur', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'KlantId' => 4, 'MedewerkerId' => 2, 'BehandelingId' => 4, 'Datum' => '2026-07-12', 'Starttijd' => '09:00:00', 'Status' => 'Afgerond', 'IsActief' => 1, 'Opmerking' => 'Contant betaald', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'KlantId' => 1, 'MedewerkerId' => 2, 'BehandelingId' => 2, 'Datum' => '2026-06-01', 'Starttijd' => '15:00:00', 'Status' => 'Geannuleerd', 'IsActief' => 1, 'Opmerking' => 'Klant was ziek', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('Bestelling')->insert([
            ['Id' => 1, 'ProductId' => 1, 'KlantId' => 1, 'Orderdatum' => '2026-07-01', 'VerwachteLeverdatum' => '2026-07-04', 'Status' => 'In behandeling', 'IsActief' => 1, 'Opmerking' => 'Klant haalt het op in de salon', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'ProductId' => 3, 'KlantId' => 3, 'Orderdatum' => '2026-06-28', 'VerwachteLeverdatum' => '2026-07-02', 'Status' => 'Verzonden', 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'ProductId' => 2, 'KlantId' => 2, 'Orderdatum' => '2026-06-15', 'VerwachteLeverdatum' => '2026-06-18', 'Status' => 'Geleverd', 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'ProductId' => 4, 'KlantId' => 4, 'Orderdatum' => '2026-07-02', 'VerwachteLeverdatum' => '2026-07-06', 'Status' => 'Nieuw', 'IsActief' => 1, 'Opmerking' => 'Met spoed', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'ProductId' => 5, 'KlantId' => 1, 'Orderdatum' => '2026-05-10', 'VerwachteLeverdatum' => '2026-05-14', 'Status' => 'Geleverd', 'IsActief' => 1, 'Opmerking' => 'Kam voor thuisgebruik', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);

        DB::table('Bestelregel')->insert([
            ['Id' => 1, 'BestellingId' => 1, 'Aantal' => 2, 'PrijsPerStuk' => 19.95, 'IsActief' => 1, 'Opmerking' => 'Twee flessen shampoo', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 2, 'BestellingId' => 2, 'Aantal' => 4, 'PrijsPerStuk' => 12.95, 'IsActief' => 1, 'Opmerking' => 'Voorraad voor kleuring', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 3, 'BestellingId' => 3, 'Aantal' => 1, 'PrijsPerStuk' => 16.50, 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 4, 'BestellingId' => 4, 'Aantal' => 1, 'PrijsPerStuk' => 24.95, 'IsActief' => 1, 'Opmerking' => null, 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
            ['Id' => 5, 'BestellingId' => 5, 'Aantal' => 3, 'PrijsPerStuk' => 4.50, 'IsActief' => 1, 'Opmerking' => 'Extra kammen', 'DatumAangemaakt' => now(), 'DatumGewijzigd' => now()],
        ]);
    }
}
