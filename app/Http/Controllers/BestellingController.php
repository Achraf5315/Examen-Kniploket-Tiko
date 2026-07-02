<?php

namespace App\Http\Controllers;

use App\Models\Bestelling;
use App\Models\Klant;
use App\Models\Product;
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
        $producten = Product::select('Productnaam')->get();
        $klanten = Klant::select('Naam')->get();

        return view('bestellingen.create', [
            'producten' => $producten,
            'klanten' => $klanten,
        ]);
    }
}
