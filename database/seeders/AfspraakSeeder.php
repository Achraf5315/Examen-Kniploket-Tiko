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
        Afspraak::factory()->count(5)->create();
    }
}
