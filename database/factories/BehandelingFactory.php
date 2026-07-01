<?php

namespace Database\Factories;

use App\Models\Behandeling;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Behandeling>
 */
class BehandelingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Naam' => fake()->words(2, true),
            'Prijs' => fake()->randomFloat(2, 5, 250),
            'DuurMinuten' => fake()->numberBetween(15, 180),
            'IsActief' => true,
            'Opmerking' => fake()->optional()->sentence(),
        ];
    }
}
