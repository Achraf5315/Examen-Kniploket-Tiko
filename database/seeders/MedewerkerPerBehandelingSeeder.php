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
        MedewerkerPerBehandeling::factory()->count(5)->create();
    }
}
