<?php

namespace App\Http\Controllers;

use App\Models\Behandeling;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class BehandelingController extends Controller
{
    /**
     * Toont het behandelingsoverzicht via de stored procedure.
     */
    public function index(): View
    {
        // Overzicht wordt conform exameneis via stored procedure opgehaald.
        $behandelingen = Behandeling::getAllViaStoredProcedure();

        return view('behandelingen.index', compact('behandelingen'));
    }

    /**
     * Toont het formulier om een nieuwe behandeling toe te voegen.
     */
    public function create(): View
    {
        // Alleen actieve producten worden getoond om koppeling met behandelingen te maken.
        $producten = Product::query()
            ->where('IsActief', 1)
            ->orderBy('Productnaam')
            ->get(['Id', 'Productnaam']);

        return view('behandelingen.create', compact('producten'));
    }

    /**
     * Slaat een nieuwe behandeling op in de database.
     */
    public function store(Request $request): RedirectResponse
    {
        // Technische log: markeer de start van een toevoeg-poging voor traceerbaarheid.
        Log::info('Start toevoegen behandeling', ['ip' => $request->ip()]);

        // Server-side validatie als extra beveiligingslaag naast de HTML5 validatie.
        // Prijs: maximaal 3 cijfers voor de komma (max 999.99) om de DECIMAL(6,2)-kolom te beschermen.
        // DuurMinuten: maximaal 3 cijfers in totaal (max 999) zodat de duur nooit overloopt.
        $validated = $request->validate([
            'Naam' => ['required', 'string', 'max:100', 'unique:Behandeling,Naam'],
            'Prijs' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'DuurMinuten' => ['required', 'integer', 'min:1', 'max:999'],
            'Opmerking' => ['nullable', 'string', 'max:255'],
            'Producten' => ['nullable', 'array'],
            'Producten.*' => ['integer', 'distinct', 'exists:Product,Id'],
        ], $this->validationMessages(), $this->validationAttributes());

        try {
            // Dubbelcheck om race conditions of handmatige requests op te vangen.
            $bestaatAl = Behandeling::query()
                ->whereRaw('LOWER(Naam) = ?', [mb_strtolower($validated['Naam'])])
                ->exists();

            if ($bestaatAl) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Deze behandeling bestaat al.');
            }

            // Stored procedure verwacht één product-id; bij meerdere selecties nemen we de eerste.
            $productId = null;
            if (isset($validated['Producten']) && is_array($validated['Producten']) && $validated['Producten'] !== []) {
                $productId = (int) $validated['Producten'][0];
            }

            Behandeling::insertViaProcedure(
                $validated['Naam'],
                (float) $validated['Prijs'],
                (int) $validated['DuurMinuten'],
                $validated['Opmerking'] ?? null,
                $productId
            );

            // Technische log: bevestig de geslaagde insert inclusief gekoppeld product.
            Log::info('Behandeling toegevoegd', [
                'naam' => $validated['Naam'],
                'product_id' => $productId,
            ]);

            return redirect()
                ->route('behandelingen.index')
                ->with('success', 'Behandeling is succesvol toegevoegd.');
        } catch (\Throwable $exception) {
            // Technische logging voor ontwikkelaars/beheer.
            Log::error('Fout bij toevoegen behandeling', [
                'foutmelding' => $exception->getMessage(),
                'bestand' => $exception->getFile(),
                'regel' => $exception->getLine(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Toevoegen is mislukt. Probeer het opnieuw.');
        }
    }

    /**
     * Toont het formulier om een bestaande behandeling te wijzigen.
     */
    public function edit(Behandeling $behandeling): View
    {
        // Productselectie inclusief bestaande koppelingen tonen in wijzigformulier.
        $producten = Product::query()
            ->where('IsActief', 1)
            ->orderBy('Productnaam')
            ->get(['Id', 'Productnaam']);

        $geselecteerdeProducten = DB::table('BehandelingPerProduct')
            ->where('BehandelingId', $behandeling->Id)
            ->where('IsActief', 1)
            ->pluck('ProductId')
            ->map(static fn ($id) => (int) $id)
            ->all();

        return view('behandelingen.edit', compact('behandeling', 'producten', 'geselecteerdeProducten'));
    }

    /**
     * Werkt een bestaande behandeling bij in de database.
     */
    public function update(Request $request, Behandeling $behandeling): RedirectResponse
    {
        // Technische log: markeer de start van een wijzig-poging voor traceerbaarheid.
        Log::info('Start wijzigen behandeling', ['behandeling_id' => $behandeling->Id]);

        // Validatie met unieke naam, waarbij de huidige record wordt uitgesloten.
        // Zelfde limieten als bij toevoegen: Prijs max 999.99 (3 cijfers), DuurMinuten max 999.
        $validated = $request->validate([
            'Naam' => ['required', 'string', 'max:100', 'unique:Behandeling,Naam,' . $behandeling->Id . ',Id'],
            'Prijs' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'DuurMinuten' => ['required', 'integer', 'min:1', 'max:999'],
            'Opmerking' => ['nullable', 'string', 'max:255'],
            'Producten' => ['nullable', 'array'],
            'Producten.*' => ['integer', 'distinct', 'exists:Product,Id'],
        ], $this->validationMessages(), $this->validationAttributes());

        try {
            // Stored procedure verwacht één product-id; bij meerdere selecties nemen we de eerste.
            $productId = null;
            if (isset($validated['Producten']) && is_array($validated['Producten']) && $validated['Producten'] !== []) {
                $productId = (int) $validated['Producten'][0];
            }

            Behandeling::updateViaProcedure(
                (int) $behandeling->Id,
                $validated['Naam'],
                (float) $validated['Prijs'],
                (int) $validated['DuurMinuten'],
                $validated['Opmerking'] ?? null,
                $productId
            );

            // Technische log: bevestig de geslaagde update.
            Log::info('Behandeling gewijzigd', [
                'behandeling_id' => $behandeling->Id,
                'product_id' => $productId,
            ]);

            return redirect()
                ->route('behandelingen.index')
                ->with('success', 'Behandeling is succesvol gewijzigd.');
        } catch (\Throwable $exception) {
            // Technische logging voor foutanalyse.
            Log::error('Fout bij wijzigen behandeling', [
                'behandeling_id' => $behandeling->Id,
                'foutmelding' => $exception->getMessage(),
                'bestand' => $exception->getFile(),
                'regel' => $exception->getLine(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Wijzigen is mislukt. Probeer het opnieuw.');
        }
    }

    /**
     * Verwijdert een behandeling na controle van de bevestigingscode.
     */
    public function destroy(Request $request, Behandeling $behandeling): RedirectResponse
    {
        // Controleer of de gebruiker exact de vereiste code heeft ingevoerd.
        $request->validate([
            'bevestigingscode' => ['required', 'string', 'in:VERWIJDEREN'],
        ], [
            'bevestigingscode.required' => 'Voer de bevestigingscode in.',
            'bevestigingscode.in' => 'De ingevoerde bevestigingscode is onjuist.',
        ], [
            'bevestigingscode' => 'bevestigingscode',
        ]);

        try {
            Behandeling::deleteViaProcedure((int) $behandeling->Id);

            // Technische log: bevestig de geslaagde (soft-)delete.
            Log::info('Behandeling verwijderd', ['behandeling_id' => $behandeling->Id]);

            return redirect()
                ->route('behandelingen.index')
                ->with('success', 'Behandeling is succesvol verwijderd.');
        } catch (\Throwable $exception) {
            // Technische logging voor beheerders.
            Log::error('Fout bij verwijderen behandeling', [
                'behandeling_id' => $behandeling->Id,
                'foutmelding' => $exception->getMessage(),
                'bestand' => $exception->getFile(),
                'regel' => $exception->getLine(),
            ]);

            return redirect()
                ->route('behandelingen.index')
                ->with('error', 'Verwijderen is mislukt. Probeer het opnieuw.');
        }
    }

    /**
     * Nederlandse validatiemeldingen voor consistente gebruikersfeedback.
     */
    private function validationMessages(): array
    {
        return [
            'required' => 'Het veld :attribute is verplicht.',
            'string' => 'Het veld :attribute moet tekst zijn.',
            'max' => 'Het veld :attribute mag maximaal :max tekens bevatten.',
            'min.numeric' => 'Het veld :attribute moet minimaal :min zijn.',
            'max.numeric' => 'Het veld :attribute mag maximaal :max zijn.',
            'integer' => 'Het veld :attribute moet een heel getal zijn.',
            'numeric' => 'Het veld :attribute moet een getal zijn.',
            'array' => 'Het veld :attribute moet een lijst zijn.',
            'distinct' => 'Een geselecteerd item in :attribute komt dubbel voor.',
            'exists' => 'Een geselecteerd item in :attribute bestaat niet.',
            'unique' => 'De :attribute is al in gebruik.',
            // Exacte tekst uit de userstory: bij een dubbele naam moet precies deze melding verschijnen.
            'Naam.unique' => 'Deze behandeling bestaat al.',
        ];
    }

    /**
     * Nederlandse veldnamen voor leesbare validatiefouten.
     */
    private function validationAttributes(): array
    {
        return [
            'Naam' => 'naam',
            'Prijs' => 'prijs',
            'DuurMinuten' => 'duur in minuten',
            'Opmerking' => 'opmerking',
            'Producten' => 'producten',
            'Producten.*' => 'product',
        ];
    }
}
