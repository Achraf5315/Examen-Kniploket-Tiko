<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rol::query()->firstOrCreate(['Rolnaam' => 'Klant'], [
            'IsActief' => true,
            'Opmerking' => null,
        ]);

        Rol::query()->firstOrCreate(['Rolnaam' => 'Eigenaar'], [
            'IsActief' => true,
            'Opmerking' => null,
        ]);
    }
}
