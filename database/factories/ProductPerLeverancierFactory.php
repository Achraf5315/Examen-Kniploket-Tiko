<?php

namespace Database\Factories;

use App\Models\ProductPerLeverancier;
use App\Models\Leverancier;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductPerLeverancier>
 */
class ProductPerLeverancierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ProductId' => Product::factory(),
            'LeverancierId' => Leverancier::factory(),
            'IsActief' => true,
            'Opmerking' => fake()->optional()->sentence(),
        ];
    }
}
