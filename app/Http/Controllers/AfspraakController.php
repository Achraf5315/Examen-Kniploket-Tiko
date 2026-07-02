<?php

namespace App\Http\Controllers;

use App\Models\Afspraak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

/**
 * Controller voor het beheren van afspraken (volledig CRUD).
 *
 * Alle databasebewerkingen verlopen via stored procedures die worden
 * aangeroepen in het Afspraak-model. Iedere methode bevat een try/catch
 * en schrijft logregels voor zowel succes als fouten.
 */
class AfspraakController extends Controller
{
    /**
     * Toont het overzicht van alle afspraken (Read).
     *
     * Met de filterbalk kan er gezocht worden op klant, medewerker,
     * behandeling, datum of status.
     */
    public function index(Request $request): View
    {
        // Zoekterm uit de filterbalk (leeg wanneer er niet gefilterd wordt)
        $zoekterm = trim((string) $request->query('zoek', ''));

        try {
            // Haal alle afspraken op via de stored procedure spAfspraakOverzicht
            $afspraken = Afspraak::getAllAfspraken();

            // Pas de filterbalk toe op het opgehaalde overzicht
            if ($zoekterm !== '') {
                $afspraken = $afspraken->filter(function (object $afspraak) use ($zoekterm): bool {
                    $doorzoekbaar = implode(' ', [
                        $afspraak->KlantNaam,
                        $afspraak->MedewerkerNaam,
                        $afspraak->BehandelingNaam,
                        $afspraak->Datum,
                        $afspraak->Status,
                    ]);

                    return str_contains(mb_strtolower($doorzoekbaar), mb_strtolower($zoekterm));
                });
            }

            Log::info('Afspraakoverzicht succesvol geladen.', [
                'aantal' => $afspraken->count(),
                'zoekterm' => $zoekterm,
                'gebruiker_id' => $request->user()?->Id,
            ]);

            return view('Afspraak.index', [
                'afspraken' => $afspraken,
                'zoekterm' => $zoekterm,
            ]);
        } catch (Throwable $fout) {
            // Scenario: de gegevens kunnen niet worden opgehaald uit de database
            Log::error('Afspraakoverzicht kan niet worden geladen.', [
                'foutmelding' => $fout->getMessage(),
            ]);

            return view('Afspraak.index', [
                'afspraken' => collect(),
                'zoekterm' => $zoekterm,
                'foutmelding' => 'Het afspraakoverzicht kan niet worden geladen. Probeer het later opnieuw.',
            ]);
        }
    }
}
