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
        // Probeer bestellingen op te halen en log eventuele fouten
        try {
            $bestellingen = $this->bestelling->getAllBestellingen();

            Log::info('Bestelling opgehaald', ['bestellingen' => $bestellingen]);
        } catch (\Exception $e) {

            Log::error('Fout bij het ophalen van bestellingen: '.$e->getMessage());
            $bestellingen = [];
        }

        // Geef de bestellingen door aan de view
        return view('bestellingen.index', [
            'bestellingen' => $bestellingen,
        ]);
    }

    public function create()
    {
        // Haal alleen producten en klanten op voor het aanmaken van een bestelling
        $producten = Product::select('Id', 'Productnaam')->get();
        $klanten = Klant::select('Id', 'Naam')->get();


        // Geef de producten en klanten door aan de view
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

        // Log een waarschuwing als de verwachte leverdatum te vroeg is
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

    public function edit($id)
    {
        // Probeer de bestelling op te halen en log eventuele fouten
        $bestelling = $this->bestelling->findBestellingById($id);


        // Controleer of de bestelling bestaat, zo niet, redirect met een foutmelding
        if (!$bestelling) {
            return redirect()->route('bestellingen.index')->with('error', 'Bestelling niet gevonden.');
        }

        // Haal alleen producten en klanten op voor het bewerken van een bestelling
        $producten = Product::select('Id', 'Productnaam')->get();
        $klanten = Klant::select('Id', 'Naam')->get();

        $selectedProduct = Product::select('Id', 'Productnaam')->find($bestelling->ProductId);
        if ($selectedProduct && $producten->where('Id', $selectedProduct->Id)->isEmpty()) {
            $producten = $producten->prepend($selectedProduct);
        }


        // Geef de bestelling, producten en klanten door aan de view
        return view('bestellingen.edit', [
            'bestelling' => $bestelling,
            'producten' => $producten,
            'klanten' => $klanten,
        ]);
    }

    public function update(Request $request, $id)
    {
        $bestelling = $this->bestelling->findBestellingById($id);

        // data valideren die we binnenkrijgen van de form
        $validatedData = $request->validate([
            'ProductNaam' => 'required|exists:Product,Id',
            'KlantNaam' => 'required|exists:Klant,Id',
            'Orderdatum' => 'required|date',
            'VerwachteLeverdatum' => 'required|date',
            'Status' => 'required|string|max:30',
        ]);

        // zet de data in een array voor de update
        $updateData = [
            'ProductId' => $validatedData['ProductNaam'],
            'KlantId' => $validatedData['KlantNaam'],
            'Orderdatum' => $validatedData['Orderdatum'],
            'VerwachteLeverdatum' => $validatedData['VerwachteLeverdatum'],
            'Status' => $validatedData['Status'],
        ];

        // Controleer of de verwachte leverdatum minimaal 2 dagen in de toekomst ligt
        try {
            $verwachte = Carbon::parse($validatedData['VerwachteLeverdatum']);
        } catch (\Exception $e) {
            Log::error('Ongeldige datum voor VerwachteLeverdatum: '.$validatedData['VerwachteLeverdatum']);

            return redirect()->back()->with(['error','Ongeldige datum voor VerwachteLeverdatum.']);
        }

        // Log een waarschuwing als de verwachte leverdatum te vroeg is
        if ($verwachte->lt(Carbon::now()->addDays(2))) {
            Log::warning('Verwachte leverdatum is te vroeg', ['VerwachteLeverdatum' => $validatedData['VerwachteLeverdatum']]);
            
            session()->flash('error', 'De verwachte leverdatum moet minimaal 2 dagen na de orderdatum liggen.');

            return redirect()->back();
        }

        // Controleer of de bestelling al is geleverd of verzonden, zo ja, geef een foutmelding en redirect terug
        if($bestelling->Status === 'Geleverd' || $validatedData['Status'] === 'Verzonden') {
            session()->flash('error', 'Een bestelling die al is geleverd of verzonden kan niet worden gewijzigd');
            return redirect()->back();
        }

        // Probeer de bestelling bij te werken en log eventuele fouten
        try {
            $this->bestelling->updateBestelling($id, $updateData);
            Log::info('Bestelling bijgewerkt', ['bestelling' => $updateData]);
        } catch (\Exception $e) {
            Log::error('Fout bij het bijwerken van bestelling: '.$e->getMessage());

            return redirect()->back()->with('error', 'Er is een fout opgetreden bij het bijwerken van de bestelling.');
        }

        // Redirect naar de indexpagina met een succesbericht
        session()->flash('success', 'Bestelling bijgewerkt.');

        return redirect()->route('bestellingen.index');
    }

    public function destroy($id)
    {
        // Probeer de bestelling te verwijderen en log eventuele fouten
        try {
            $this->bestelling->deleteBestelling($id);
            Log::info('Bestelling verwijderd', ['bestelling_id' => $id]);
        } catch (\Exception $e) {
            Log::error('Fout bij het verwijderen van bestelling: '.$e->getMessage());

            return redirect()->back()->with('error', 'Er is een fout opgetreden bij het verwijderen van de bestelling.');
        }

        // Redirect naar de indexpagina met een succesbericht
        session()->flash('success', 'Bestelling verwijderd.');

        return redirect()->route('bestellingen.index');
    }
    
}
