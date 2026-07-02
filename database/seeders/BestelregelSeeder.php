<?php

namespace Database\Seeders;

use App\Models\Bestelregel;
use App\Models\Bestelling;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BestelregelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Bestelregel::factory()->count(5)->create();
    }
}
