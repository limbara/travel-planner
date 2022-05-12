<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransportPlan>
 */
class TransportPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'lat_from' => $this->faker->latitude(),
            'lng_from' => $this->faker->longitude(),
            'lat_to' => $this->faker->latitude(),
            'lng_to' => $this->faker->longitude(),
            'address_from' => $this->faker->address(),
            'address_to' => $this->faker->address(),
            'transportation' => $this->faker->randomElement(['walk', 'car', 'mrt'])
        ];
    }
}
