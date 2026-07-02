<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Bestelling;

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
            Log::info('Bestelling opgehaald: ' . json_encode($bestellingen));
        } catch (\Exception $e) {
            // Handle the exception, e.g., log it or return an error response
            Log::error('Error met het ophalen van bestellingen: ' . $e->getMessage());
            return response()->json(['error' => 'Error met het ophalen van bestellingen ' . $e->getMessage()], 500);
        }

        return view('bestellingen.index', [
            'bestellingen' => $bestellingen
        ]);
    }
}
