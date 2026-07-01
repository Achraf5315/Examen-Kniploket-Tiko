<?php

namespace Database\Seeders;

use App\Models\Bestelregel;
use App\Models\Bestelling;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BestelregelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bestelling = Bestelling::query()->first();

        if (! $bestelling) {
            return;
        }

        Bestelregel::factory()->create([
            'BestellingId' => $bestelling->getKey(),
            'Aantal' => 2,
            'PrijsPerStuk' => 12.50,
        ]);
    }
}
