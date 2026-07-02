<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Afspraak extends TikoModel
{
    /** @use HasFactory<\Database\Factories\AfspraakFactory> */
    use HasFactory;

    protected $table = 'Afspraak';

    public function klant(): BelongsTo
    {
        return $this->belongsTo(Klant::class, 'KlantId', 'Id');
    }

    public function medewerker(): BelongsTo
    {
        return $this->belongsTo(Medewerker::class, 'MedewerkerId', 'Id');
    }

    public function behandeling(): BelongsTo
    {
        return $this->belongsTo(Behandeling::class, 'BehandelingId', 'Id');
    }

    /**
     * Haalt het volledige afsprakenoverzicht op via de stored procedure spAfspraakOverzicht.
     *
     * De stored procedure bevat JOINs met de tabellen Klant, Medewerker en Behandeling
     * en berekent per afspraak ook de eindtijd.
     *
     * @return Collection<int, object> Alle actieve afspraken inclusief gekoppelde namen
     */
    public static function getAllAfspraken(): Collection
    {
        Log::debug('Stored procedure spAfspraakOverzicht wordt aangeroepen.');

        // Roep de stored procedure aan; het resultaat is een lijst van rijen (objecten)
        $afspraken = DB::select('CALL spAfspraakOverzicht()');

        return collect($afspraken);
    }

    /**
     * Voegt een nieuwe afspraak toe via de stored procedure spAfspraakToevoegen.
     *
     * De stored procedure controleert zelf (met een JOIN op Behandeling) of de
     * afspraak overlapt met een bestaande afspraak van dezelfde medewerker.
     * Bij overlap gooit MySQL een SIGNAL-fout die als QueryException terugkomt.
     *
     * @param  array<string, mixed>  $gegevens  De gevalideerde formuliergegevens
     * @return int Het Id van de nieuw aangemaakte afspraak
     */
    public static function createAfspraak(array $gegevens): int
    {
        Log::debug('Stored procedure spAfspraakToevoegen wordt aangeroepen.', $gegevens);

        // Roep de stored procedure aan; deze geeft het nieuwe Id terug
        $resultaat = DB::select('CALL spAfspraakToevoegen(?, ?, ?, ?, ?)', [
            $gegevens['KlantId'],
            $gegevens['MedewerkerId'],
            $gegevens['BehandelingId'],
            $gegevens['Datum'],
            $gegevens['Starttijd'],
        ]);

        $nieuwId = (int) ($resultaat[0]->Id ?? 0);

        Log::info('Afspraak toegevoegd via stored procedure.', ['afspraak_id' => $nieuwId]);

        return $nieuwId;
    }
}
