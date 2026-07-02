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
        // Server-side validatie als extra beveiligingslaag naast HTML5 validatie.
        $validated = $request->validate([
            'Naam' => ['required', 'string', 'max:100', 'unique:Behandeling,Naam'],
            'Prijs' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'DuurMinuten' => ['required', 'integer', 'min:1', 'max:600'],
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
                    ->with('error', 'Deze behandeling bestaat al. Kies een andere naam.');
            }

            DB::transaction(function () use ($validated): void {
                $behandeling = Behandeling::create([
                    'Naam' => $validated['Naam'],
                    'Prijs' => $validated['Prijs'],
                    'DuurMinuten' => $validated['DuurMinuten'],
                    // Status staat functioneel standaard op actief.
                    'IsActief' => true,
                    'Opmerking' => $validated['Opmerking'] ?? null,
                ]);

                // Koppel geselecteerde producten met standaard hoeveelheid 1.
                $pivotData = [];
                foreach ($validated['Producten'] ?? [] as $productId) {
                    $pivotData[$productId] = [
                        'Aantal' => 1,
                        'IsActief' => 1,
                        'Opmerking' => null,
                        'DatumAangemaakt' => now(),
                        'DatumGewijzigd' => now(),
                    ];
                }

                if ($pivotData !== []) {
                    $behandeling->producten()->sync($pivotData);
                }
            });

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
        // Validatie met unieke naam, waarbij de huidige record wordt uitgesloten.
        $validated = $request->validate([
            'Naam' => ['required', 'string', 'max:100', 'unique:Behandeling,Naam,' . $behandeling->Id . ',Id'],
            'Prijs' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'DuurMinuten' => ['required', 'integer', 'min:1', 'max:600'],
            'Opmerking' => ['nullable', 'string', 'max:255'],
            'Producten' => ['nullable', 'array'],
            'Producten.*' => ['integer', 'distinct', 'exists:Product,Id'],
        ], $this->validationMessages(), $this->validationAttributes());

        try {
            DB::transaction(function () use ($behandeling, $validated): void {
                $behandeling->update([
                    'Naam' => $validated['Naam'],
                    'Prijs' => $validated['Prijs'],
                    'DuurMinuten' => $validated['DuurMinuten'],
                    // Status staat functioneel standaard op actief.
                    'IsActief' => true,
                    'Opmerking' => $validated['Opmerking'] ?? null,
                ]);

                // Synchroniseer productkoppelingen op basis van de huidige formulierkeuze.
                $pivotData = [];
                foreach ($validated['Producten'] ?? [] as $productId) {
                    $pivotData[$productId] = [
                        'Aantal' => 1,
                        'IsActief' => 1,
                        'Opmerking' => null,
                        'DatumAangemaakt' => now(),
                        'DatumGewijzigd' => now(),
                    ];
                }

                $behandeling->producten()->sync($pivotData);
            });

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
            'bevestigingscode.in' => 'De bevestigingscode moet exact VERWIJDEREN zijn.',
        ], [
            'bevestigingscode' => 'bevestigingscode',
        ]);

        try {
            $behandeling->delete();

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
