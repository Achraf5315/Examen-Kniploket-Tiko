<?php

namespace App\Http\Controllers;

use App\Models\Bestelling;
use App\Models\Klant;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BestellingController extends Controller
{
    private Bestelling $bestelling;

    public function __construct(Bestelling $bestelling)
    {
        $this->bestelling = $bestelling;
    }

    public function index()
    {
        try {
            $bestellingen = $this->bestelling->getAllBestellingen();

            Log::info('Bestelling opgehaald', ['bestellingen' => $bestellingen]);
        } catch (\Exception $e) {

            Log::error('Fout bij het ophalen van bestellingen: '.$e->getMessage());
            $bestellingen = [];
        }

        return view('bestellingen.index', [
            'bestellingen' => $bestellingen,
        ]);
    }

    public function create()
    {
        // Haal alleen producten en klanten op voor het aanmaken van een bestelling
        $producten = Product::select('Id', 'Productnaam')->get();
        $klanten = Klant::select('Id', 'Naam')->get();

        return view('bestellingen.create', [
            'producten' => $producten,
            'klanten' => $klanten,
        ]);
    }

    public function store(Request $request)
    {
        // data valideren die we binnenkrijgen van de form
        $validatedData = $request->validate([
            'ProductNaam' => 'required|exists:Product,Id',
            'KlantNaam' => 'required|exists:Klant,Id',
            'Orderdatum' => 'required|date',
            'VerwachteLeverdatum' => 'required|date',
            'Status' => 'required|string|max:30',
        ]);

        // Controleer of de verwachte leverdatum minimaal 2 dagen in de toekomst ligt
        try {
            $verwachte = Carbon::parse($validatedData['VerwachteLeverdatum']);
        } catch (\Exception $e) {
            Log::error('Ongeldige datum voor VerwachteLeverdatum: '.$validatedData['VerwachteLeverdatum']);

            return redirect()->back()->with(['error','Ongeldige datum voor VerwachteLeverdatum.']);
        }

        if ($verwachte->lt(Carbon::now()->addDays(2))) {
            Log::warning('Verwachte leverdatum is te vroeg', ['VerwachteLeverdatum' => $validatedData['VerwachteLeverdatum']]);
            
            session()->flash('error', 'De verwachte leverdatum moet minimaal 2 dagen na de orderdatum liggen.');

            return redirect()->back();
        }

        // Probeer de bestelling op te slaan en log eventuele fouten
        try {
            $this->bestelling->createBestelling($validatedData);
            Log::info('Bestelling opgeslagen', ['bestelling' => $validatedData]);
        } catch (\Exception $e) {
            Log::error('Fout bij het opslaan van bestelling: '.$e->getMessage());

            return redirect()->back()->with('error', 'Er is een fout opgetreden bij het opslaan van de bestelling.');
        }

        // Redirect naar de indexpagina met een succesbericht
        session()->flash('success', 'Bestelling toegevoegd.');

        return redirect()->route('bestellingen.index');
    }
}
