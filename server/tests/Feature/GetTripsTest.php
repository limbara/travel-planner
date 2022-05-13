<?php

namespace Tests\Feature;

use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;

class GetTripsTest extends BaseTest
{
    public function testGetTripsWithoutToken()
    {
        $this->json('GET', 'api/trips', [], ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'error_code' => 401,
                'error_message' => 'Unauthenticated.'
            ]);
    }

    public function testGetTrips()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $jsonResponse = $this->json('GET', 'api/trips', [], ['Accept' => 'application/json'])->assertStatus(200)->decodeResponseJson();

        $jsonResponse->assertStructure([
            'message',
            'data' => [
                'trips'
            ]
        ]);
    }

    public function testShowTripWithoutToken()
    {
        $user = User::factory()->create();

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $this->json('GET', "api/trips/{$trip->id}", [], ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'error_code' => 401,
                'error_message' => 'Unauthenticated.'
            ]);
    }

    public function testShowTrip()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $this->json('GET', "api/trips/{$trip->id}", [], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $trip->id,
                'title' => $trip->title,
                'description' => $trip->description,
                'origin' => $trip->origin,
                'destination' => $trip->destination,
                'date_from' => date_format($trip->date_from, 'Y-m-d H:i:s'),
                'date_to' => date_format($trip->date_to, 'Y-m-d H:i:s'),
                'user_id' => strval($user->id),
                'created_at' => Carbon::createFromDate($trip->created_at)->toISOString(),
                'updated_at' => Carbon::createFromDate($trip->updated_at)->toISOString(),
                'deleted_at' => null
            ])
            ->assertJsonStructure([
                'data' => [
                    'trip',
                    'plans'
                ]
            ]);
    }
}
