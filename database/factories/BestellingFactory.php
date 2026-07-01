<?php

namespace Database\Factories;

use App\Models\Bestelling;
use App\Models\Klant;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bestelling>
 */
class BestellingFactory extends Factory
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
            'KlantId' => Klant::factory(),
            'Orderdatum' => fake()->date(),
            'VerwachteLeverdatum' => fake()->optional()->date(),
            'Status' => fake()->randomElement(['Nieuw', 'InBehandeling', 'Verzonden']),
            'IsActief' => true,
            'Opmerking' => fake()->optional()->sentence(),
        ];
    }
}
