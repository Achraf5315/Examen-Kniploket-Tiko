<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Feature tests voor de AfspraakController.
 *
 * De testdata komt uit de bestaande DatabaseSeeder/DummyDataSeeder van het team,
 * zodat de tests dezelfde gegevens gebruiken als de rest van het project.
 * De tests draaien op de aparte testdatabase kniploket_tiko_test (zie phpunit.xml).
 */
class AfspraakControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Draai de seeder voor iedere test zodat er altijd dezelfde testdata aanwezig is.
     */
    protected $seed = true;

    /**
     * Hulpmethode: geeft de eigenaar (rol Admin) uit de seeder terug.
     */
    private function eigenaar(): User
    {
        return User::where('Email', 'eigenaar@tiko.com')->firstOrFail();
    }

    /**
     * Hulpmethode: geeft een gebruiker met alleen de rol Klant terug.
     */
    private function klant(): User
    {
        return User::where('Email', 'sanne.visser@gmail.com')->firstOrFail();
    }

    // ---------- Feature: Afspraak Overzicht ----------

    /**
     * Scenario: afspraakoverzicht bekijken lukt.
     * De eigenaar ziet het overzicht met alle afspraken uit de database.
     */
    public function test_eigenaar_ziet_het_afspraakoverzicht(): void
    {
        $reactie = $this->actingAs($this->eigenaar())->get(route('afspraken.index'));

        $reactie->assertOk();
        $reactie->assertViewIs('Afspraak.index');

        // Gegevens uit de dummy data moeten zichtbaar zijn in het overzicht
        $reactie->assertSee('Sanne Visser');
        $reactie->assertSee('Lisa van der Meer');
        $reactie->assertSee('Wassen, Knippen & Drogen (Dames)');
    }

    /**
     * Scenario: afspraakoverzicht kan niet worden geladen.
     * Bij een databasefout verschijnt er een duidelijke foutmelding.
     */
    public function test_overzicht_toont_foutmelding_bij_databasefout(): void
    {
        $eigenaar = $this->eigenaar();

        // Simuleer een databasefout: de aanroep van de stored procedure mislukt
        DB::partialMock()
            ->shouldReceive('select')
            ->andThrow(new \RuntimeException('Databaseverbinding mislukt (gesimuleerd voor de test)'));

        $reactie = $this->actingAs($eigenaar)->get(route('afspraken.index'));

        $reactie->assertOk();
        $reactie->assertSee('Het afspraakoverzicht kan niet worden geladen');
    }

    /**
     * De filterbalk filtert het overzicht, bijvoorbeeld op klantnaam.
     */
    public function test_filterbalk_filtert_het_overzicht(): void
    {
        $reactie = $this->actingAs($this->eigenaar())->get(route('afspraken.index', ['zoek' => 'Bram']));

        $reactie->assertOk();
        $reactie->assertSee('Bram Meijer');
        $reactie->assertDontSee('Sanne Visser');
    }

    /**
     * Een gebruiker met alleen de rol Klant mag het beheeroverzicht niet zien.
     */
    public function test_klant_heeft_geen_toegang_tot_het_overzicht(): void
    {
        $reactie = $this->actingAs($this->klant())->get(route('afspraken.index'));

        $reactie->assertForbidden();
    }

    /**
     * Een gast (niet ingelogd) wordt doorgestuurd naar de loginpagina.
     */
    public function test_gast_wordt_doorgestuurd_naar_login(): void
    {
        $reactie = $this->get(route('afspraken.index'));

        $reactie->assertRedirect(route('login'));
    }

    // ---------- Feature: Afspraak Toevoegen ----------

    /**
     * Het formulier voor het toevoegen van een afspraak is bereikbaar.
     */
    public function test_toevoegen_formulier_is_bereikbaar(): void
    {
        $reactie = $this->actingAs($this->eigenaar())->get(route('afspraken.create'));

        $reactie->assertOk();
        $reactie->assertViewIs('Afspraak.Toevoegen');
        $reactie->assertSee('Afspraak Toevoegen');
    }

    /**
     * Scenario: een afspraak wordt succesvol toegevoegd.
     * Geldige gegevens zonder overlap worden opgeslagen en verschijnen in het overzicht.
     */
    public function test_afspraak_toevoegen_lukt_met_geldige_gegevens(): void
    {
        // Medewerker 1 heeft op 2026-07-10 alleen een afspraak van 10:00 tot 10:45,
        // dus 13:00 is vrij (geen overlap)
        $reactie = $this->actingAs($this->eigenaar())->post(route('afspraken.store'), [
            'KlantId' => 2,
            'MedewerkerId' => 1,
            'BehandelingId' => 2,
            'Datum' => '2026-07-10',
            'Starttijd' => '13:00',
        ]);

        $reactie->assertRedirect(route('afspraken.index'));
        $reactie->assertSessionHas('succes');

        // De afspraak staat in de database met de standaardstatus Gereserveerd
        $this->assertDatabaseHas('Afspraak', [
            'KlantId' => 2,
            'MedewerkerId' => 1,
            'BehandelingId' => 2,
            'Datum' => '2026-07-10',
            'Starttijd' => '13:00:00',
            'Status' => 'Gereserveerd',
        ]);
    }

    /**
     * Scenario: een afspraak toevoegen lukt niet vanwege overlap.
     * De stored procedure weigert de afspraak en de gebruiker krijgt een melding.
     */
    public function test_afspraak_toevoegen_faalt_bij_overlap(): void
    {
        // Medewerker 1 heeft al een afspraak op 2026-07-10 van 10:00 tot 10:45,
        // dus een nieuwe afspraak om 10:30 overlapt daarmee
        $reactie = $this->actingAs($this->eigenaar())->post(route('afspraken.store'), [
            'KlantId' => 2,
            'MedewerkerId' => 1,
            'BehandelingId' => 2,
            'Datum' => '2026-07-10',
            'Starttijd' => '10:30',
        ]);

        $reactie->assertSessionHas('fout', 'De afspraak overlapt met een bestaande afspraak.');

        // De overlappende afspraak is niet opgeslagen
        $this->assertDatabaseMissing('Afspraak', [
            'Datum' => '2026-07-10',
            'Starttijd' => '10:30:00',
        ]);
    }

    /**
     * Ongeldige invoer wordt tegengehouden door de Form Request validatie.
     */
    public function test_afspraak_toevoegen_faalt_bij_ongeldige_invoer(): void
    {
        $reactie = $this->actingAs($this->eigenaar())
            ->from(route('afspraken.create'))
            ->post(route('afspraken.store'), []);

        $reactie->assertRedirect(route('afspraken.create'));
        $reactie->assertSessionHasErrors(['KlantId', 'MedewerkerId', 'BehandelingId', 'Datum', 'Starttijd']);
    }

    // ---------- Feature: Afspraak Wijzigen ----------

    /**
     * Het wijzigformulier toont de bestaande gegevens van de afspraak.
     */
    public function test_wijzigen_formulier_toont_bestaande_gegevens(): void
    {
        // Afspraak 1 uit de seeder: Sanne Visser bij Lisa van der Meer op 2026-07-10 om 10:00
        $reactie = $this->actingAs($this->eigenaar())->get(route('afspraken.edit', 1));

        $reactie->assertOk();
        $reactie->assertViewIs('Afspraak.Wijzigen');
        $reactie->assertSee('Afspraak Wijzigen');
        $reactie->assertSee('value="2026-07-10"', false);
        $reactie->assertSee('value="10:00"', false);
    }

    /**
     * Scenario: een afspraak wordt succesvol gewijzigd.
     */
    public function test_afspraak_wijzigen_lukt_met_geldige_gegevens(): void
    {
        // Verplaats afspraak 1 naar 15:00; medewerker 1 heeft dan geen andere afspraak
        $reactie = $this->actingAs($this->eigenaar())->put(route('afspraken.update', 1), [
            'KlantId' => 1,
            'MedewerkerId' => 1,
            'BehandelingId' => 1,
            'Datum' => '2026-07-10',
            'Starttijd' => '15:00',
        ]);

        $reactie->assertRedirect(route('afspraken.index'));
        $reactie->assertSessionHas('succes');

        // De wijziging staat in de database
        $this->assertDatabaseHas('Afspraak', [
            'Id' => 1,
            'Starttijd' => '15:00:00',
        ]);
    }

    /**
     * Scenario: een afspraak wijzigen lukt niet vanwege overlap.
     */
    public function test_afspraak_wijzigen_faalt_bij_overlap(): void
    {
        // Afspraak 2 is bij Tom Hendriks (medewerker 2) op 2026-07-10 van 11:30 tot 12:00.
        // Afspraak 1 verplaatsen naar medewerker 2 om 11:45 levert dus overlap op.
        $reactie = $this->actingAs($this->eigenaar())->put(route('afspraken.update', 1), [
            'KlantId' => 1,
            'MedewerkerId' => 2,
            'BehandelingId' => 1,
            'Datum' => '2026-07-10',
            'Starttijd' => '11:45',
        ]);

        $reactie->assertSessionHas('fout', 'De afspraak overlapt met een bestaande afspraak.');

        // De oorspronkelijke afspraak is niet gewijzigd
        $this->assertDatabaseHas('Afspraak', [
            'Id' => 1,
            'MedewerkerId' => 1,
            'Starttijd' => '10:00:00',
        ]);
    }

    /**
     * Het wijzigen van een onbekende afspraak geeft een nette melding.
     */
    public function test_wijzigen_van_onbekende_afspraak_geeft_melding(): void
    {
        $reactie = $this->actingAs($this->eigenaar())->get(route('afspraken.edit', 999));

        $reactie->assertRedirect(route('afspraken.index'));
        $reactie->assertSessionHas('fout', 'De afspraak is niet gevonden.');
    }
}
