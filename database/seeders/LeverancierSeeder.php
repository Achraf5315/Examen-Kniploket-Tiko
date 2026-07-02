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
        Leverancier::factory()->count(5)->create();
    }
}
