<?php

namespace Database\Seeders;

use App\Models\Behandeling;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BehandelingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Knippen', 'Wassen', 'Stylen'] as $naam) {
            Behandeling::factory()->create([
                'Naam' => $naam,
            ]);
        }
    }
}
