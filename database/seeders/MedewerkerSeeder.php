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
        $address = Adres::query()->first();
        $owner = User::query()->where('Email', 'eigenaar@tiko.com')->first();

        if (! $address || ! $owner) {
            return;
        }

        Medewerker::factory()->create([
            'GebruikerId' => $owner->getKey(),
            'AdresId' => $address->getKey(),
            'Naam' => 'Eigenaar',
            'Telefoonnummer' => '0600000000',
            'Specialisaties' => 'Algemeen',
        ]);
    }
}
