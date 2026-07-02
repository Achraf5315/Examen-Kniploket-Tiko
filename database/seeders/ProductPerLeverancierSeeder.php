<?php

namespace Database\Seeders;

use App\Models\Leverancier;
use App\Models\Product;
use App\Models\ProductPerLeverancier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductPerLeverancierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductPerLeverancier::factory()->count(5)->create();
    }
}
