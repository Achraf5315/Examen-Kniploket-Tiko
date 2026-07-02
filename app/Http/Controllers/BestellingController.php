<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $bestellingen = $this->bestelling->getAllBestellingen();

        return view('bestellingen.index', [
            'bestellingen' => $bestellingen
        ]);
    }
}
