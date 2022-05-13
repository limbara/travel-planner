<?php

namespace Tests\Feature;

use App\Models\FlightPlan;
use App\Models\Plan;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;

class DeleteTripPlanTest extends BaseTest
{
    public function testDeleteTripPlanWithoutToken()
    {
        $user = User::factory()->create();

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $dateFrom = Carbon::createFromDate($trip->date_from)->addHour(1);
        $dateTo = $dateFrom->clone()->addHours(2);

        $plan = Plan::factory()->setSchedule(
            $dateFrom,
            $dateTo,
            $dateFrom,
            $dateTo
        )->state([
            'title' => 'Plane Flight to destination',
            'trip_id' => $trip->id
        ])->for(
            FlightPlan::factory()
                ->setSchedule($dateFrom, $dateTo),
            'plannable'
        )->create();

        $this->json('DELETE', "api/trips/{$trip->id}/plans/{$plan->id}", [], ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'error_code' => 401,
                'error_message' => 'Unauthenticated.'
            ]);
    }

    public function testDeleteTripPlanNotFound()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $dateFrom = Carbon::createFromDate($trip->date_from)->addHour(1);
        $dateTo = $dateFrom->clone()->addHours(2);

        $plan = Plan::factory()->setSchedule(
            $dateFrom,
            $dateTo,
            $dateFrom,
            $dateTo
        )->state([
            'title' => 'Plane Flight to destination',
            'trip_id' => $trip->id
        ])->for(
            FlightPlan::factory()
                ->setSchedule($dateFrom, $dateTo),
            'plannable'
        )->create();

        $modifiedPlanId = substr($plan->id, 0, strlen($plan->id) - 1) . 'a';

        $this->json('DELETE', "api/trips/{$trip->id}/plans/{$modifiedPlanId}", [], ['Accept' => 'application/json'])
            ->assertStatus(404)
            ->assertJson([
                "error_code" => 404
            ]);
    }

    public function testDeletetripPlanSuccess()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $dateFrom = Carbon::createFromDate($trip->date_from)->addHour(1);
        $dateTo = $dateFrom->clone()->addHours(2);

        $plan = Plan::factory()->setSchedule(
            $dateFrom,
            $dateTo,
            $dateFrom,
            $dateTo
        )->state([
            'title' => 'Plane Flight to destination',
            'trip_id' => $trip->id
        ])->for(
            FlightPlan::factory()
                ->setSchedule($dateFrom, $dateTo),
            'plannable'
        )->create();

        $this->json('DELETE', "api/trips/{$trip->id}/plans/{$plan->id}", [], ['Accept' => 'application/json'])
            ->assertStatus(200);

        $this->assertDatabaseMissing('plans', [
            'id' => $plan->id
        ]);

        $this->assertDatabaseMissing('flight_plans', [
            'id' => $plan->plannable_id
        ]);
    }
}
