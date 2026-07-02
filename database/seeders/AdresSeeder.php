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
        Adres::factory()->count(5)->create();
    }
}
