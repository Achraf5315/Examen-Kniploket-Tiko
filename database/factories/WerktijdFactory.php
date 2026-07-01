<?php

namespace Database\Factories;

use App\Models\Medewerker;
use App\Models\Werktijd;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Werktijd>
 */
class WerktijdFactory extends Factory
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
            'Dag' => fake()->randomElement(['Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag']),
            'Starttijd' => '09:00:00',
            'Eindtijd' => '17:00:00',
            'IsActief' => true,
            'Opmerking' => fake()->optional()->sentence(),
        ];
    }
}
