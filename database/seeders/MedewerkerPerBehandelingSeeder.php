<?php

namespace Database\Seeders;

use App\Models\Behandeling;
use App\Models\Medewerker;
use App\Models\MedewerkerPerBehandeling;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedewerkerPerBehandelingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medewerker = Medewerker::query()->first();
        $behandeling = Behandeling::query()->first();

        if (! $medewerker || ! $behandeling) {
            return;
        }

        MedewerkerPerBehandeling::factory()->create([
            'MedewerkerId' => $medewerker->getKey(),
            'BehandelingId' => $behandeling->getKey(),
        ]);
    }
}
