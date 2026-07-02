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
        BehandelingPerProduct::factory()->count(5)->create();
    }
}
