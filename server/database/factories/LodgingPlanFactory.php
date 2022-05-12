<?php

namespace Database\Factories;

use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LodgingPlan>
 */
class LodgingPlanFactory extends Factory
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
            'location_lat' => $this->faker->latitude(),
            'location_lng' => $this->faker->longitude(),
            'location_name' => $this->faker->streetName(),
            'location_address' => $this->faker->address(),
            'check_in_date' => $this->faker->dateTimeBetween('now', '+4 hours'),
            'check_out_date' => $this->faker->dateTimeBetween('+2 days', '+4 days'),
        ];
    }

    /**
     * Set Checkin Schedule 
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function setSchedule(DateTime $checkInDate, DateTime $checkOutDate)
    {
        return $this->state(function () use ($checkInDate, $checkOutDate) {
            return [
                'check_in_date' => $checkInDate,
                'check_out_date' => $checkOutDate
            ];
        });
    }
}
