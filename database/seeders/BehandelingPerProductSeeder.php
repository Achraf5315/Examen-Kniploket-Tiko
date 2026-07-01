<?php

namespace Database\Seeders;

use App\Models\Behandeling;
use App\Models\BehandelingPerProduct;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BehandelingPerProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $behandeling = Behandeling::query()->first();
        $product = Product::query()->first();

        if (! $behandeling || ! $product) {
            return;
        }

        BehandelingPerProduct::factory()->create([
            'BehandelingId' => $behandeling->getKey(),
            'ProductId' => $product->getKey(),
            'Aantal' => 1,
        ]);
    }
}
