<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests voor de BehandelingController.
 *
 * De testdata komt uit de bestaande DatabaseSeeder/DummyDataSeeder van het team en
 * draait op de aparte MySQL-testdatabase kniploket_tiko_test (zie phpunit.xml),
 * omdat het overzicht en de CRUD-acties via stored procedures verlopen.
 */
class BehandelingControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Draai de seeder voor iedere test zodat er altijd dezelfde testdata aanwezig is.
     */
    protected $seed = true;

    /**
     * Hulpmethode: geeft de eigenaar (ingelogde beheerder) uit de seeder terug.
     */
    private function eigenaar(): User
    {
        return User::where('Email', 'eigenaar@tiko.com')->firstOrFail();
    }

    // ---------- Feature: Behandeling Overzicht (Read) ----------

    /**
     * Happy path: het overzicht is bereikbaar en toont de seeder-behandelingen.
     */
    public function test_overzicht_is_bereikbaar_en_toont_behandelingen(): void
    {
        $reactie = $this->actingAs($this->eigenaar())->get(route('behandelingen.index'));

        $reactie->assertOk();
        $reactie->assertViewIs('behandelingen.index');
        $reactie->assertSee('Haar Volledig Kleuren (Kort haar)');
    }

    /**
     * Unhappy path: een gast (niet ingelogd) wordt doorgestuurd naar de login.
     */
    public function test_gast_wordt_doorgestuurd_naar_login(): void
    {
        $reactie = $this->get(route('behandelingen.index'));

        $reactie->assertRedirect(route('login'));
    }

    /**
     * Het toevoeg-formulier is bereikbaar.
     */
    public function test_toevoegen_formulier_is_bereikbaar(): void
    {
        $reactie = $this->actingAs($this->eigenaar())->get(route('behandelingen.create'));

        $reactie->assertOk();
        $reactie->assertViewIs('behandelingen.create');
        $reactie->assertSee('Behandeling toevoegen');
    }

    /**
     * Het wijzig-formulier toont de bestaande gegevens van de behandeling.
     */
    public function test_wijzigen_formulier_toont_bestaande_gegevens(): void
    {
        // Behandeling 3 uit de seeder: 'Haar Volledig Kleuren (Kort haar)', 90 minuten.
        $reactie = $this->actingAs($this->eigenaar())->get(route('behandelingen.edit', 3));

        $reactie->assertOk();
        $reactie->assertViewIs('behandelingen.edit');
        $reactie->assertSee('Haar Volledig Kleuren (Kort haar)');
        $reactie->assertSee('value="90"', false);
    }

    // ---------- Feature: Behandeling Toevoegen (Create) ----------

    /**
     * Happy path: een behandeling wordt met geldige gegevens opgeslagen en
     * de gebruiker wordt met een succesmelding teruggestuurd naar het overzicht.
     */
    public function test_behandeling_toevoegen_lukt_met_geldige_gegevens(): void
    {
        $reactie = $this->actingAs($this->eigenaar())->post(route('behandelingen.store'), [
            'Naam' => 'Föhnbehandeling Deluxe',
            'Prijs' => 34.95,
            'DuurMinuten' => 25,
            'Opmerking' => 'Testbehandeling',
            'Producten' => [1],
        ]);

        $reactie->assertRedirect(route('behandelingen.index'));
        $reactie->assertSessionHas('success');

        // De nieuwe behandeling staat actief in de database.
        $this->assertDatabaseHas('Behandeling', [
            'Naam' => 'Föhnbehandeling Deluxe',
            'DuurMinuten' => 25,
            'IsActief' => 1,
        ]);
    }

    /**
     * Unhappy path: lege invoer wordt tegengehouden door de server-side validatie.
     */
    public function test_behandeling_toevoegen_faalt_bij_lege_invoer(): void
    {
        $reactie = $this->actingAs($this->eigenaar())
            ->from(route('behandelingen.create'))
            ->post(route('behandelingen.store'), []);

        $reactie->assertRedirect(route('behandelingen.create'));
        $reactie->assertSessionHasErrors(['Naam', 'Prijs', 'DuurMinuten']);
    }

    /**
     * Unhappy path: een prijs boven 999.99 (meer dan 3 cijfers voor de komma) wordt geweigerd.
     */
    public function test_behandeling_toevoegen_faalt_bij_te_hoge_prijs(): void
    {
        $reactie = $this->actingAs($this->eigenaar())
            ->from(route('behandelingen.create'))
            ->post(route('behandelingen.store'), [
                'Naam' => 'Veel te dure behandeling',
                'Prijs' => 1000,
                'DuurMinuten' => 30,
            ]);

        $reactie->assertSessionHasErrors('Prijs');
        $this->assertDatabaseMissing('Behandeling', ['Naam' => 'Veel te dure behandeling']);
    }

    /**
     * Unhappy path: een duur boven 999 minuten (meer dan 3 cijfers) wordt geweigerd.
     */
    public function test_behandeling_toevoegen_faalt_bij_te_lange_duur(): void
    {
        $reactie = $this->actingAs($this->eigenaar())
            ->from(route('behandelingen.create'))
            ->post(route('behandelingen.store'), [
                'Naam' => 'Eindeloze behandeling',
                'Prijs' => 20,
                'DuurMinuten' => 1000,
            ]);

        $reactie->assertSessionHasErrors('DuurMinuten');
        $this->assertDatabaseMissing('Behandeling', ['Naam' => 'Eindeloze behandeling']);
    }

    /**
     * Unhappy path: een dubbele naam wordt geweigerd (unieke naam-regel).
     */
    public function test_behandeling_toevoegen_faalt_bij_dubbele_naam(): void
    {
        $reactie = $this->actingAs($this->eigenaar())
            ->from(route('behandelingen.create'))
            ->post(route('behandelingen.store'), [
                // 'Knippen & Stylen (Heren)' bestaat al in de seeder.
                'Naam' => 'Knippen & Stylen (Heren)',
                'Prijs' => 20,
                'DuurMinuten' => 30,
            ]);

        $reactie->assertSessionHasErrors('Naam');
    }
}
