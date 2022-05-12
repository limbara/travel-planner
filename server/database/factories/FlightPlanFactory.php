<?php

namespace Database\Factories;

use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FlightPlan>
 */
class FlightPlanFactory extends Factory
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
            'departure_airport' => $this->faker->city() . ' Airport',
            'arrival_airport' => $this->faker->city() . ' Airport',
            'departure_date' => $this->faker->dateTimeBetween('now', 'now'),
            'arrival_date' => $this->faker->dateTimeBetween('+1 hours', '+2 hours')
        ];
    }

    /**
     * Set Flight Schedule 
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function setSchedule(DateTime $departureDate, DateTime $arrivalDate)
    {
        return $this->state(function () use ($departureDate, $arrivalDate) {
            return [
                'departure_date' => $departureDate,
                'arrival_date' => $arrivalDate
            ];
        });
    }
}
