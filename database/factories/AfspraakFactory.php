<?php

namespace Database\Factories;

use App\Models\Afspraak;
use App\Models\Behandeling;
use App\Models\Klant;
use App\Models\Medewerker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Afspraak>
 */
class AfspraakFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'KlantId' => Klant::factory(),
            'MedewerkerId' => Medewerker::factory(),
            'BehandelingId' => Behandeling::factory(),
            'Datum' => fake()->date(),
            'Starttijd' => '10:00:00',
            'Status' => fake()->randomElement(['Gepland', 'Bezig', 'Afgerond']),
            'IsActief' => true,
            'Opmerking' => fake()->optional()->sentence(),
        ];
    }
}
