<?php

namespace Database\Seeders;

use App\Models\Medewerker;
use App\Models\Werktijd;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WerktijdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medewerker = Medewerker::query()->first();

        if (! $medewerker) {
            return;
        }

        foreach ([['Maandag', '09:00:00', '17:00:00'], ['Dinsdag', '09:00:00', '17:00:00']] as [$dag, $starttijd, $eindtijd]) {
            Werktijd::factory()->create([
                'MedewerkerId' => $medewerker->getKey(),
                'Dag' => $dag,
                'Starttijd' => $starttijd,
                'Eindtijd' => $eindtijd,
            ]);
        }
    }
}
