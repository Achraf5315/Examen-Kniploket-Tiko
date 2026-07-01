<?php

namespace Database\Seeders;

use App\Models\Leverancier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeverancierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Tiko BV', 'Hair Supply NL'] as $naam) {
            Leverancier::factory()->create([
                'Naam' => $naam,
                'Telefoonnummer' => '0611111111',
            ]);
        }
    }
}
