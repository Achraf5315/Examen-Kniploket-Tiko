<?php

namespace Database\Factories;

use App\Models\Medewerker;
use App\Models\Adres;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medewerker>
 */
class MedewerkerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'GebruikerId' => User::factory(),
            'AdresId' => Adres::factory(),
            'Naam' => fake()->name(),
            'Telefoonnummer' => fake()->numerify('##########'),
            'Specialisaties' => fake()->word(),
            'IsActief' => true,
            'Opmerking' => fake()->optional()->sentence(),
        ];
    }
}
