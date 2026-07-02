<?php

namespace Database\Seeders;

use App\Models\Behandeling;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BehandelingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Behandeling::factory()->count(5)->create();
    }
}
