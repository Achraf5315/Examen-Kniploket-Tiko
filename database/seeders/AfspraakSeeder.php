<?php

namespace Database\Seeders;

use App\Models\Afspraak;
use App\Models\Behandeling;
use App\Models\Klant;
use App\Models\Medewerker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AfspraakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $klant = Klant::query()->first();
        $medewerker = Medewerker::query()->first();
        $behandeling = Behandeling::query()->first();

        if (! $klant || ! $medewerker || ! $behandeling) {
            return;
        }

        Afspraak::factory()->create([
            'KlantId' => $klant->getKey(),
            'MedewerkerId' => $medewerker->getKey(),
            'BehandelingId' => $behandeling->getKey(),
            'Datum' => now()->addDays(1)->toDateString(),
            'Starttijd' => '10:00:00',
            'Status' => 'Gepland',
        ]);
    }
}
