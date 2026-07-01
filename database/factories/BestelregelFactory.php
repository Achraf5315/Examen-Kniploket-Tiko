<?php

namespace Database\Factories;

use App\Models\Bestelregel;
use App\Models\Bestelling;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bestelregel>
 */
class BestelregelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'BestellingId' => Bestelling::factory(),
            'Aantal' => fake()->numberBetween(1, 10),
            'PrijsPerStuk' => fake()->randomFloat(2, 1, 1000),
            'IsActief' => true,
            'Opmerking' => fake()->optional()->sentence(),
        ];
    }
}
