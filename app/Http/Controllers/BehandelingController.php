<?php

namespace App\Http\Controllers;

use App\Models\Behandeling;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        return view('behandelingen.create');
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
            'IsActief' => ['required', 'boolean'],
            'Opmerking' => ['nullable', 'string', 'max:255'],
        ]);

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

            Behandeling::create([
                'Naam' => $validated['Naam'],
                'Prijs' => $validated['Prijs'],
                'DuurMinuten' => $validated['DuurMinuten'],
                'IsActief' => (bool) $validated['IsActief'],
                'Opmerking' => $validated['Opmerking'] ?? null,
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
        return view('behandelingen.edit', compact('behandeling'));
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
            'IsActief' => ['required', 'boolean'],
            'Opmerking' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $behandeling->update([
                'Naam' => $validated['Naam'],
                'Prijs' => $validated['Prijs'],
                'DuurMinuten' => $validated['DuurMinuten'],
                'IsActief' => (bool) $validated['IsActief'],
                'Opmerking' => $validated['Opmerking'] ?? null,
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
}
