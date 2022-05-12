<?php

namespace Database\Factories;

use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
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
            'title' => $this->faker->title(),
            'description' => $this->faker->paragraph(),
            'start_date' => null,
            'end_date' => null,
            'start_time' => null,
            'end_time' => null,
        ];
    }

    /**
     * Set Plan Schedule 
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function setSchedule(DateTime $startDate = null, DateTime $endDate = null, DateTime $startTime = null, DateTime $endTime = null)
    {
        return $this->state(function () use ($startDate, $endDate, $startTime, $endTime) {
            return [
                'start_date' => $startDate ? date_format($startDate, 'Y-m-d') : null,
                'end_date' => $endDate ? date_format($endDate, 'Y-m-d') : null,
                'start_time' => $startTime ? date_format($startTime, 'H:i:s') : null,
                'end_time' => $endTime ? date_format($endTime, 'H:i:s') : null
            ];
        });
    }
}
