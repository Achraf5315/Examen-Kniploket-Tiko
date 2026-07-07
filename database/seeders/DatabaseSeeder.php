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
        $eigenaar = User::create([
            'Gebruikersnaam' => 'Eigenaar',
            'Email' => 'eigenaar@tiko.com',
            'Wachtwoord' => Hash::make('achraf123'),
            'IsActief' => true,
            'DatumAangemaakt' => now(),
            'DatumGewijzigd' => now(),
        ]);

        // Koppel de eigenaar direct aan de Admin-rol. Zonder deze koppeling
        // wijst de 'rol:Admin,Medewerker'-middleware de eigenaar af met een
        // 403, ook al bestaat het account (bug: rechtenfout op lege database).
        $adminRolId = DB::table('Rol')->where('Rolnaam', 'Admin')->value('Id');

        if ($adminRolId === null) {
            $adminRolId = DB::table('Rol')->insertGetId([
                'Rolnaam' => 'Admin',
                'IsActief' => true,
                'Opmerking' => 'Beheerder van het systeem',
                'DatumAangemaakt' => now(),
                'DatumGewijzigd' => now(),
            ]);
        }

        DB::table('RolPerGebruiker')->insert([
            'GebruikerId' => $eigenaar->getKey(),
            'RolId' => $adminRolId,
            'IsActief' => true,
            'Opmerking' => 'Standaard admin',
            'DatumAangemaakt' => now(),
            'DatumGewijzigd' => now(),
        ]);

        $this->call(DummyDataSeeder::class);
    }
}
