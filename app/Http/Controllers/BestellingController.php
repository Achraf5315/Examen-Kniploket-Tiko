<?php

namespace App\Http\Controllers;

use App\Models\Bestelling;
use App\Models\Klant;
use App\Models\Product;
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
        $validatedData = $request->validate([
            'ProductNaam' => 'required|exists:Product,Id',
            'KlantNaam' => 'required|exists:Klant,Id',
            'Orderdatum' => 'required|date',
            'VerwachteLeverdatum' => 'required|date',
            'Status' => 'required|string|max:30',
        ]);

        try {
            $this->bestelling->createBestelling($validatedData);
            Log::info('Bestelling opgeslagen', ['bestelling' => $validatedData]);
        } catch (\Exception $e) {
            Log::error('Fout bij het opslaan van bestelling: '.$e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Er is een fout opgetreden bij het opslaan van de bestelling.']);
        }

        return redirect()->route('bestellingen.index')->with('success', 'Bestelling opgeslagen.');
    }
}
