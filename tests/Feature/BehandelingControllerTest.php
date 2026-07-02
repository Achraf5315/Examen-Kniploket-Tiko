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
}
