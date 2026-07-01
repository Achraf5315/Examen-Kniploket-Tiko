<?php

namespace Database\Seeders;

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
        $this->call([
            RolSeeder::class,
        ]);

        // eigenaar account
        $owner = User::factory()->create([
            'name' => 'Eigenaar',
            'email' => 'eigenaar@tiko.com',
            'password' => Hash::make('achraf123'),
        ]);

        $eigenaarRoleId = DB::table('Rol')->where('Rolnaam', 'Eigenaar')->value('Id');

        DB::table('RolPerGebruiker')->insert([
            'GebruikerId' => $owner->getKey(),
            'RolId' => $eigenaarRoleId,
            'IsActief' => true,
            'Opmerking' => null,
            'DatumAangemaakt' => now(),
            'DatumGewijzigd' => now(),
        ]);

        $this->call([
            KlantSeeder::class,
            AdresSeeder::class,
            CategorieSeeder::class,
            LeverancierSeeder::class,
            ProductSeeder::class,
            ProductPerLeverancierSeeder::class,
            BehandelingSeeder::class,
            BehandelingPerProductSeeder::class,
            MedewerkerSeeder::class,
            MedewerkerPerBehandelingSeeder::class,
            WerktijdSeeder::class,
            AfspraakSeeder::class,
            BestellingSeeder::class,
            BestelregelSeeder::class,
        ]);

    }
}
