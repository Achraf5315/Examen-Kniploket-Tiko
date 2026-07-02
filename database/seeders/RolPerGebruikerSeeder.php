<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\RolPerGebruiker;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolPerGebruikerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $rollen = Rol::all();

        foreach ($users as $user) {
            // Assign a random role to each user
            $randomRol = $rollen->random();
            RolPerGebruiker::create([
                'UserId' => $user->id,
                'RolId' => $randomRol->id,
            ]);
        }
    }
}
