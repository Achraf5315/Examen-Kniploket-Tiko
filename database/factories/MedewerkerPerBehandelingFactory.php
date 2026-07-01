<?php

namespace Database\Factories;

use App\Models\MedewerkerPerBehandeling;
use App\Models\Behandeling;
use App\Models\Medewerker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedewerkerPerBehandeling>
 */
class MedewerkerPerBehandelingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'MedewerkerId' => Medewerker::factory(),
            'BehandelingId' => Behandeling::factory(),
            'IsActief' => true,
            'Opmerking' => fake()->optional()->sentence(),
        ];
    }
}
