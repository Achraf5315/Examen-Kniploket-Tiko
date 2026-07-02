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
        Bestelling::factory()->count(5)->create();
    }
}
