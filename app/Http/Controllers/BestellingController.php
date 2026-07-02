<?php

namespace App\Http\Controllers;

use App\Models\Bestelling;
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

            Log::error('Fout bij het ophalen van bestellingen: ' . $e->getMessage());
            $bestellingen = [];
        }

        return view('bestellingen.index', [
            'bestellingen' => $bestellingen,
        ]);
    }

    public function create()
    {
        return view('bestellingen.create');
    }
}
