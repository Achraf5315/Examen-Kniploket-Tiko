<?php

namespace Database\Seeders;

use App\Models\Adres;
use App\Models\Klant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $klant = Klant::query()->first();

        if (! $klant) {
            return;
        }

        Adres::factory()->create([
            'KlantId' => $klant->getKey(),
            'Straatnaam' => 'Voorbeeldstraat',
            'Huisnummer' => 12,
            'Toevoeging' => 'A',
            'Postcode' => '1000AA',
            'Plaats' => 'Amsterdam',
        ]);
    }
}
