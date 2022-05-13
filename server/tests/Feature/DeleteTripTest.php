<?php

namespace Tests\Feature;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Str;

class DeleteTripTest extends BaseTest
{
    public function testDeleteTripWithoutToken()
    {
        $uuid = Str::uuid();

        $this->json('DELETE', "api/trips/{$uuid}", [], ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'error_code' => 401,
                'error_message' => 'Unauthenticated.'
            ]);
    }

    public function testDeleteNotFoundTrip()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $uuid = Str::uuid();

        $this->json('DELETE', "api/trips/{$uuid}", [], ['Accept' => 'application/json'])
            ->assertStatus(404)
            ->assertJson([
                'error_code' => 404,
                'error_message' => 'Trip Not Found'
            ]);
    }

    public function testDeleteTripSuccess()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $trip = Trip::factory()->state([
            'user_id' => $user->id
        ])->create();

        $deleteTripResponse = $this->json('DELETE', "api/trips/{$trip->id}", [], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->decodeResponseJson();

        $deleteTripResponse->assertStructure([
            'message',
            'data' => [
                'trip' => [
                    'id',
                    'title',
                    'description',
                    'origin',
                    'destination',
                    'date_from',
                    'date_to',
                    'user_id',
                    'created_at',
                    'updated_at',
                ]
            ]
        ]);

        $trip = $deleteTripResponse['data']['trip'];

        $this->json('GET', 'api/trips', [], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonMissing([
                'id' => $trip['id']
            ]);
    }
}
