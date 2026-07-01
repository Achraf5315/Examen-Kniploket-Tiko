<?php

namespace Database\Factories;

use App\Models\BehandelingPerProduct;
use App\Models\Behandeling;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BehandelingPerProduct>
 */
class BehandelingPerProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'BehandelingId' => Behandeling::factory(),
            'ProductId' => Product::factory(),
            'Aantal' => fake()->numberBetween(1, 5),
            'IsActief' => true,
            'Opmerking' => fake()->optional()->sentence(),
        ];
    }
}
