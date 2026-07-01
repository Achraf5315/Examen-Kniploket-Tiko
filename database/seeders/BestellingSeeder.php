<?php

namespace Database\Seeders;

use App\Models\Bestelling;
use App\Models\Klant;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BestellingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = Product::query()->first();
        $klant = Klant::query()->first();

        if (! $product || ! $klant) {
            return;
        }

        Bestelling::factory()->create([
            'ProductId' => $product->getKey(),
            'KlantId' => $klant->getKey(),
            'Orderdatum' => now()->toDateString(),
            'VerwachteLeverdatum' => now()->addDays(3)->toDateString(),
            'Status' => 'Nieuw',
        ]);
    }
}
