<?php

namespace Database\Factories;

use App\Models\RolPerGebruiker;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RolPerGebruiker>
 */
class RolPerGebruikerFactory extends Factory
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
            'RolId' => Rol::factory(),
            'IsActief' => true,
            'Opmerking' => fake()->optional()->sentence(),
        ];
    }
}
