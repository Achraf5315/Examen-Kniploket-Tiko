<?php

namespace Database\Seeders;

use App\Models\RolPerGebruiker;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // eigenaar account
        User::create([
            'Gebruikersnaam' => 'Eigenaar',
            'Email' => 'eigenaar@tiko.com',
            'Wachtwoord' => Hash::make('achraf123'),
            'IsActief' => true,
            'DatumAangemaakt' => now(),
            'DatumGewijzigd' => now(),
        ]);
        $this->call(DummyDataSeeder::class);
    }
}
