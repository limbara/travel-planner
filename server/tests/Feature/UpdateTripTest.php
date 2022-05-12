<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Str;

class UpdateTripTest extends BaseTest
{
    public function testUpdateTripWithoutToken()
    {
        $uuid = Str::uuid();

        $this->json('POST', "api/trips/{$uuid}", [], ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'error_code' => 401,
                'error_message' => 'Unauthenticated.'
            ]);
    }

    public function testUpdateNotFoundTrip()
    {
        $this->seed();

        $token = $this->getLoginToken();
        $uuid = Str::uuid();

        $tripData = [
            'title' => 'Trip To Singapore',
            'description' => 'Trip To Singapore',
            'origin' => 'Indonesia',
            'destination' => 'Singapore',
            'date_from' => '2022-06-01 00:00:00',
            'date_to' => '2022-06-07 23:59:59'
        ];

        $this->json('POST', "api/trips/{$uuid}", $tripData, ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
            ->assertStatus(404)
            ->assertJson([
                'error_code' => 404,
                'error_message' => 'Trip Not Found'
            ]);
    }

    public function testUpdateTripNonAlphaNumSpaceTitle()
    {
        $this->seed();

        $token = $this->getLoginToken();

        $user  = User::where('email', 'Nico@example.com')->first();
        $trip = $user->trips()->first();

        $tripData = [
            'title' => 'Trip To Singapore #2',
            'description' => 'Trip To Singapore',
            'origin' => 'Indonesia',
            'destination' => 'Singapore',
            'date_from' => '2022-06-01 00:00:00',
            'date_to' => '2022-06-07 23:59:59'
        ];

        $this->json('POST', "api/trips/{$trip->id}", $tripData, ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
            ->assertStatus(400)
            ->assertJson([
                'error_code' => 400,
                'errors' => [
                    'title' => ['title may only contain letters, numeric and space.']
                ]
            ])
            ->assertJsonStructure([
                'error_code',
                'error_message',
                'errors'
            ]);
    }

    public function testUpdateTripNonAlphaNumSpaceOrigin()
    {
        $this->seed();

        $token = $this->getLoginToken();

        $user  = User::where('email', 'Nico@example.com')->first();
        $trip = $user->trips()->first();

        $tripData = [
            'title' => 'Trip To Singapore',
            'description' => 'Trip To Singapore',
            'origin' => 'Indonesia #1',
            'destination' => 'Singapore',
            'date_from' => '2022-06-01 00:00:00',
            'date_to' => '2022-06-07 23:59:59'
        ];

        $this->json('POST', "api/trips/{$trip->id}", $tripData, ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
            ->assertStatus(400)
            ->assertJson([
                'error_code' => 400,
                'errors' => [
                    'origin' => ['origin may only contain letters, numeric and space.']
                ]
            ])
            ->assertJsonStructure([
                'error_code',
                'error_message',
                'errors'
            ]);
    }

    public function testUpdateTripNonAlphaNumSpaceDestination()
    {
        $this->seed();

        $token = $this->getLoginToken();

        $user  = User::where('email', 'Nico@example.com')->first();
        $trip = $user->trips()->first();

        $tripData = [
            'title' => 'Trip To Singapore',
            'description' => 'Trip To Singapore',
            'origin' => 'Indonesia',
            'destination' => 'Singapore #1',
            'date_from' => '2022-06-01 00:00:00',
            'date_to' => '2022-06-07 23:59:59'
        ];

        $this->json('POST', "api/trips/{$trip->id}", $tripData, ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
            ->assertStatus(400)
            ->assertJson([
                'error_code' => 400,
                'errors' => [
                    'destination' => ['destination may only contain letters, numeric and space.']
                ]
            ])
            ->assertJsonStructure([
                'error_code',
                'error_message',
                'errors'
            ]);
    }

    public function testUpdateTripDateFromInThePast()
    {
        $this->seed();

        $token = $this->getLoginToken();

        $user  = User::where('email', 'Nico@example.com')->first();
        $trip = $user->trips()->first();

        $tripData = [
            'title' => 'Trip To Singapore',
            'description' => 'Trip To Singapore',
            'origin' => 'Indonesia',
            'destination' => 'Singapore',
            'date_from' => '2022-05-01 00:00:00',
            'date_to' => '2022-06-07 23:59:59'
        ];

        $this->json('POST', "api/trips/{$trip->id}", $tripData, ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
            ->assertStatus(400)
            ->assertJson([
                'error_code' => 400,
                'errors' => [
                    'date_from' => ['The date from must be a date after now.']
                ]
            ])
            ->assertJsonStructure([
                'error_code',
                'error_message',
                'errors'
            ]);
    }

    public function testUpdateTripDateToBeforeDateFrom()
    {
        $this->seed();

        $token = $this->getLoginToken();

        $user  = User::where('email', 'Nico@example.com')->first();
        $trip = $user->trips()->first();

        $tripData = [
            'title' => 'Trip To Singapore',
            'description' => 'Trip To Singapore',
            'origin' => 'Indonesia',
            'destination' => 'Singapore',
            'date_from' => '2022-06-01 00:00:00',
            'date_to' => '2022-05-07 23:59:59'
        ];

        $this->json('POST', "api/trips/{$trip->id}", $tripData, ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
            ->assertStatus(400)
            ->assertJson([
                'error_code' => 400,
                'errors' => [
                    'date_to' => ['The date to must be a date after date from.']
                ]
            ])
            ->assertJsonStructure([
                'error_code',
                'error_message',
                'errors'
            ]);
    }

    public function testUpdateTripSuccess()
    {
        $this->seed();

        $token = $this->getLoginToken();

        $user  = User::where('email', 'Nico@example.com')->first();
        $trip = $user->trips()->first();

        $tripData = [
            'title' => 'Trip To Singapore',
            'description' => 'Trip To Singapore',
            'origin' => 'Indonesia',
            'destination' => 'Singapore',
            'date_from' => '2022-06-01 00:00:00',
            'date_to' => '2022-06-07 23:59:59'
        ];

        $updateTripResponse = $this->json('POST', "api/trips/{$trip->id}", $tripData, ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
            ->assertStatus(200)
            ->decodeResponseJson();

        $updateTripResponse->assertStructure([
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

        $trip = $updateTripResponse['data']['trip'];

        $this->json('GET', 'api/trips', [], ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $trip['id'],
                'title' => $trip['title'],
                'description' => $trip['description'],
                'origin' => $trip['origin'],
                'destination' => $trip['destination'],
                'date_from' => $trip['date_from'],
                'date_to' => $trip['date_to']
            ]);
    }
}
