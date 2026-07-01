<?php

namespace Database\Seeders;

use App\Models\Klant;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KlantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Klant Demo',
            'email' => 'klant@tiko.com',
            'password' => Hash::make('klant123'),
        ]);

        $roleId = Rol::query()->where('Rolnaam', 'Klant')->value('Id');

        if ($roleId) {
            DB::table('RolPerGebruiker')->insert([
                'GebruikerId' => $user->getKey(),
                'RolId' => $roleId,
                'IsActief' => true,
                'Opmerking' => null,
                'DatumAangemaakt' => now(),
                'DatumGewijzigd' => now(),
            ]);
        }

        Klant::factory()->create([
            'GebruikerId' => $user->getKey(),
            'Naam' => 'Klant Demo',
            'Telefoonnummer' => '0612345678',
            'WensenAllergieen' => 'geen',
        ]);
    }
}
