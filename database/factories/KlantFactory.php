<?php

namespace Database\Factories;

use App\Models\Klant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Klant>
 */
class KlantFactory extends Factory
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
            'Naam' => fake()->name(),
            'Telefoonnummer' => fake()->numerify('##########'),
            'WensenAllergieen' => fake()->optional()->words(3, true),
            'IsActief' => true,
            'Opmerking' => fake()->optional()->sentence(),
        ];
    }
}
