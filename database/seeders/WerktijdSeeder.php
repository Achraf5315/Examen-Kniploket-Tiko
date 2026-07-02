<?php

namespace Database\Seeders;

use App\Models\Medewerker;
use App\Models\Werktijd;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WerktijdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Werktijd::factory()->count(5)->create();
    }
}
