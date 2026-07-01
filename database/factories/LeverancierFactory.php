<?php

namespace Database\Factories;

use App\Models\Leverancier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Leverancier>
 */
class LeverancierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Naam' => fake()->company(),
            'Telefoonnummer' => fake()->numerify('##########'),
            'IsActief' => true,
            'Opmerking' => fake()->optional()->sentence(),
        ];
    }
}
