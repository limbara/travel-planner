<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $origin = sprintf("%s-%s", $this->faker->country(), $this->faker->city());
        $destination = sprintf("%s-%s", $this->faker->country(), $this->faker->city());

        return [
            'id' => $this->faker->uuid(),
            'title' => "Visiting {$destination}",
            'description' => $this->faker->paragraph(),
            'origin' => $origin,
            'destination' => $destination,
            'date_from' => $this->faker->dateTimeBetween('now', '+5days'),
            'date_to' => $this->faker->dateTimeBetween('+5days', '+10days')
        ];
    }
}
