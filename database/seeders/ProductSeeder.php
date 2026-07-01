<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorie = Categorie::query()->first();

        if (! $categorie) {
            return;
        }

        Product::factory()->create([
            'Productnaam' => 'Shampoo Basic',
            'EanCode' => '8710000000001',
            'CategorieId' => $categorie->getKey(),
            'Prijs' => 12.50,
            'Voorraad' => 20,
            'MinimumVoorraad' => 5,
        ]);

        Product::factory()->create([
            'Productnaam' => 'Conditioner Plus',
            'EanCode' => '8710000000002',
            'CategorieId' => $categorie->getKey(),
            'Prijs' => 14.95,
            'Voorraad' => 15,
            'MinimumVoorraad' => 4,
        ]);
    }
}
