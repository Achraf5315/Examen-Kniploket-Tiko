<?php

namespace App\Http\Controllers;

use App\Http\Requests\AfspraakToevoegenRequest;
use App\Http\Requests\AfspraakVerwijderenRequest;
use App\Http\Requests\AfspraakWijzigenRequest;
use App\Models\Afspraak;
use App\Models\Behandeling;
use App\Models\Klant;
use App\Models\Medewerker;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
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

    /**
     * Toont het formulier voor het toevoegen van een afspraak (Create).
     */
    public function create(): View|RedirectResponse
    {
        try {
            // Keuzelijsten voor het formulier: alleen actieve klanten, medewerkers en behandelingen
            $klanten = Klant::where('IsActief', 1)->orderBy('Naam')->get();
            $medewerkers = Medewerker::where('IsActief', 1)->orderBy('Naam')->get();
            $behandelingen = Behandeling::where('IsActief', 1)->orderBy('Naam')->get();

            Log::info('Formulier afspraak toevoegen geopend.');

            return view('Afspraak.Toevoegen', [
                'klanten' => $klanten,
                'medewerkers' => $medewerkers,
                'behandelingen' => $behandelingen,
            ]);
        } catch (Throwable $fout) {
            Log::error('Formulier afspraak toevoegen kan niet worden geladen.', [
                'foutmelding' => $fout->getMessage(),
            ]);

            return redirect()
                ->route('afspraken.index')
                ->with('fout', 'Het formulier kan niet worden geladen. Probeer het later opnieuw.');
        }
    }

    /**
     * Slaat een nieuwe afspraak op via de stored procedure (Create).
     *
     * De validatie gebeurt in AfspraakToevoegenRequest; de overlapcontrole
     * gebeurt in de stored procedure spAfspraakToevoegen.
     */
    public function store(AfspraakToevoegenRequest $request): RedirectResponse
    {
        try {
            // Voeg de afspraak toe via het model (stored procedure)
            $nieuwId = Afspraak::createAfspraak($request->validated());

            Log::info('Afspraak succesvol toegevoegd.', [
                'afspraak_id' => $nieuwId,
                'gebruiker_id' => $request->user()?->Id,
            ]);

            // Terugkoppeling naar de eindgebruiker via een flash-melding
            return redirect()
                ->route('afspraken.index')
                ->with('succes', 'De afspraak is succesvol toegevoegd.');
        } catch (QueryException $fout) {
            // Scenario: de afspraak overlapt met een bestaande afspraak (SIGNAL uit de stored procedure)
            if ($this->isOverlapFout($fout)) {
                Log::warning('Afspraak toevoegen geweigerd: overlap met een bestaande afspraak.', [
                    'invoer' => $request->validated(),
                ]);

                return back()
                    ->withInput()
                    ->with('fout', 'De afspraak overlapt met een bestaande afspraak.');
            }

            Log::error('Databasefout bij het toevoegen van een afspraak.', [
                'foutmelding' => $fout->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('fout', 'De afspraak kan niet worden opgeslagen. Probeer het later opnieuw.');
        } catch (Throwable $fout) {
            Log::error('Onverwachte fout bij het toevoegen van een afspraak.', [
                'foutmelding' => $fout->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('fout', 'De afspraak kan niet worden opgeslagen. Probeer het later opnieuw.');
        }
    }

    /**
     * Toont het formulier voor het wijzigen van een afspraak (Update).
     */
    public function edit(int $id): View|RedirectResponse
    {
        try {
            // Haal de afspraak op via de stored procedure spAfspraakDetails
            $afspraak = Afspraak::getAfspraakById($id);

            if ($afspraak === null) {
                Log::warning('Afspraak niet gevonden bij het openen van het wijzigformulier.', [
                    'afspraak_id' => $id,
                ]);

                return redirect()
                    ->route('afspraken.index')
                    ->with('fout', 'De afspraak is niet gevonden.');
            }

            // Keuzelijsten voor het formulier: alleen actieve klanten, medewerkers en behandelingen
            $klanten = Klant::where('IsActief', 1)->orderBy('Naam')->get();
            $medewerkers = Medewerker::where('IsActief', 1)->orderBy('Naam')->get();
            $behandelingen = Behandeling::where('IsActief', 1)->orderBy('Naam')->get();

            Log::info('Formulier afspraak wijzigen geopend.', ['afspraak_id' => $id]);

            return view('Afspraak.Wijzigen', [
                'afspraak' => $afspraak,
                'klanten' => $klanten,
                'medewerkers' => $medewerkers,
                'behandelingen' => $behandelingen,
            ]);
        } catch (Throwable $fout) {
            Log::error('Formulier afspraak wijzigen kan niet worden geladen.', [
                'afspraak_id' => $id,
                'foutmelding' => $fout->getMessage(),
            ]);

            return redirect()
                ->route('afspraken.index')
                ->with('fout', 'Het formulier kan niet worden geladen. Probeer het later opnieuw.');
        }
    }

    /**
     * Slaat de wijzigingen van een afspraak op via de stored procedure (Update).
     *
     * De validatie gebeurt in AfspraakWijzigenRequest; de overlapcontrole
     * gebeurt in de stored procedure spAfspraakWijzigen.
     */
    public function update(AfspraakWijzigenRequest $request, int $id): RedirectResponse
    {
        try {
            // Wijzig de afspraak via het model (stored procedure)
            Afspraak::updateAfspraak($id, $request->validated());

            Log::info('Afspraak succesvol gewijzigd.', [
                'afspraak_id' => $id,
                'gebruiker_id' => $request->user()?->Id,
            ]);

            // Terugkoppeling naar de eindgebruiker via een flash-melding
            return redirect()
                ->route('afspraken.index')
                ->with('succes', 'De afspraak is succesvol gewijzigd.');
        } catch (QueryException $fout) {
            // Scenario: de gewijzigde afspraak overlapt met een bestaande afspraak
            if ($this->isOverlapFout($fout)) {
                Log::warning('Afspraak wijzigen geweigerd: overlap met een bestaande afspraak.', [
                    'afspraak_id' => $id,
                    'invoer' => $request->validated(),
                ]);

                return back()
                    ->withInput()
                    ->with('fout', 'De afspraak overlapt met een bestaande afspraak.');
            }

            // De afspraak bestaat niet (meer): terug naar het overzicht met een melding
            if ($this->isNietGevondenFout($fout)) {
                Log::warning('Afspraak wijzigen mislukt: afspraak niet gevonden.', [
                    'afspraak_id' => $id,
                ]);

                return redirect()
                    ->route('afspraken.index')
                    ->with('fout', 'De afspraak is niet gevonden.');
            }

            Log::error('Databasefout bij het wijzigen van een afspraak.', [
                'afspraak_id' => $id,
                'foutmelding' => $fout->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('fout', 'De wijzigingen kunnen niet worden opgeslagen. Probeer het later opnieuw.');
        } catch (Throwable $fout) {
            Log::error('Onverwachte fout bij het wijzigen van een afspraak.', [
                'afspraak_id' => $id,
                'foutmelding' => $fout->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('fout', 'De wijzigingen kunnen niet worden opgeslagen. Probeer het later opnieuw.');
        }
    }

    /**
     * Toont de bevestigingspagina voor het verwijderen van een afspraak (Delete).
     */
    public function delete(int $id): View|RedirectResponse
    {
        try {
            // Haal de afspraak op zodat de gebruiker ziet wat er verwijderd wordt
            $afspraak = Afspraak::getAfspraakById($id);

            if ($afspraak === null) {
                Log::warning('Afspraak niet gevonden bij het openen van de verwijderpagina.', [
                    'afspraak_id' => $id,
                ]);

                return redirect()
                    ->route('afspraken.index')
                    ->with('fout', 'De afspraak is niet gevonden.');
            }

            Log::info('Bevestigingspagina afspraak verwijderen geopend.', ['afspraak_id' => $id]);

            return view('Afspraak.Verwijderen', ['afspraak' => $afspraak]);
        } catch (Throwable $fout) {
            Log::error('Verwijderpagina kan niet worden geladen.', [
                'afspraak_id' => $id,
                'foutmelding' => $fout->getMessage(),
            ]);

            return redirect()
                ->route('afspraken.index')
                ->with('fout', 'De pagina kan niet worden geladen. Probeer het later opnieuw.');
        }
    }

    /**
     * Verwijdert een afspraak definitief via de stored procedure (Delete).
     *
     * De AfspraakVerwijderenRequest controleert eerst of de gebruiker het
     * bevestigingswoord VERWIJDEREN juist heeft ingetypt.
     */
    public function destroy(AfspraakVerwijderenRequest $request, int $id): RedirectResponse
    {
        try {
            // Verwijder de afspraak via het model (stored procedure)
            Afspraak::deleteAfspraak($id);

            Log::info('Afspraak succesvol verwijderd.', [
                'afspraak_id' => $id,
                'gebruiker_id' => $request->user()?->Id,
            ]);

            // Terugkoppeling naar de eindgebruiker via een flash-melding
            return redirect()
                ->route('afspraken.index')
                ->with('succes', 'De afspraak is succesvol verwijderd.');
        } catch (QueryException $fout) {
            // De afspraak bestaat niet (meer): terug naar het overzicht met een melding
            if ($this->isNietGevondenFout($fout)) {
                Log::warning('Afspraak verwijderen mislukt: afspraak niet gevonden.', [
                    'afspraak_id' => $id,
                ]);

                return redirect()
                    ->route('afspraken.index')
                    ->with('fout', 'De afspraak is niet gevonden.');
            }

            Log::error('Databasefout bij het verwijderen van een afspraak.', [
                'afspraak_id' => $id,
                'foutmelding' => $fout->getMessage(),
            ]);

            return back()
                ->with('fout', 'De afspraak kan niet worden verwijderd. Probeer het later opnieuw.');
        } catch (Throwable $fout) {
            Log::error('Onverwachte fout bij het verwijderen van een afspraak.', [
                'afspraak_id' => $id,
                'foutmelding' => $fout->getMessage(),
            ]);

            return back()
                ->with('fout', 'De afspraak kan niet worden verwijderd. Probeer het later opnieuw.');
        }
    }

    /**
     * Controleert of de databasefout een overlapmelding uit de stored procedure is.
     *
     * MySQL geeft foutcode 1644 (ER_SIGNAL_EXCEPTION) wanneer een stored procedure
     * een SIGNAL SQLSTATE '45000' afgeeft.
     */
    private function isOverlapFout(QueryException $fout): bool
    {
        return ($fout->errorInfo[1] ?? null) === 1644
            && str_contains($fout->getMessage(), 'overlapt');
    }

    /**
     * Controleert of de databasefout een 'niet gevonden'-melding uit de stored procedure is.
     */
    private function isNietGevondenFout(QueryException $fout): bool
    {
        return ($fout->errorInfo[1] ?? null) === 1644
            && str_contains($fout->getMessage(), 'niet gevonden');
    }
}
