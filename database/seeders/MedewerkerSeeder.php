<?php

namespace Database\Seeders;

use App\Models\Adres;
use App\Models\Medewerker;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedewerkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Medewerker::factory()->count(5)->create();
    }
}
