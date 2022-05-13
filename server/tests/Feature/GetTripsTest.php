<?php

namespace Tests\Feature;

use App\Models\Trip;
use App\Models\User;

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
}
