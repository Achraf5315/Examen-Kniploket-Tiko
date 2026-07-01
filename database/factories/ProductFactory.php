<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Categorie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Productnaam' => fake()->words(2, true),
            'EanCode' => fake()->unique()->ean13(),
            'CategorieId' => Categorie::factory(),
            'Prijs' => fake()->randomFloat(2, 1, 9999),
            'Voorraad' => fake()->numberBetween(0, 500),
            'MinimumVoorraad' => fake()->numberBetween(0, 50),
            'IsActief' => true,
            'Opmerking' => fake()->optional()->sentence(),
        ];
    }
}
