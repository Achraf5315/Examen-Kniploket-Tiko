<?php

namespace Database\Factories;

use App\Models\Adres;
use App\Models\Klant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Adres>
 */
class AdresFactory extends Factory
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
            'Straatnaam' => fake()->streetName(),
            'Huisnummer' => fake()->numberBetween(1, 250),
            'Toevoeging' => fake()->optional()->bothify('?#'),
            'Postcode' => fake()->postcode(),
            'Plaats' => fake()->city(),
            'IsActief' => true,
            'Opmerking' => fake()->optional()->sentence(),
        ];
    }
}
