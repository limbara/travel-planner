<?php

namespace Tests\Feature;

class CreateTripTest extends BaseTest
{
    public function testCreateTripWithOutToken()
    {
        $this->json('POST', 'api/trips', [], ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'error_code' => 401,
                'error_message' => 'Unauthenticated.'
            ]);
    }

    public function testCreateTripNonAlphaNumSpaceTitle()
    {
        $this->seed();

        $token = $this->getLoginToken();

        $tripData = [
            'title' => 'Trip To Singapore #2',
            'description' => 'Trip To Singapore',
            'origin' => 'Indonesia',
            'destination' => 'Singapore',
            'date_from' => '2022-06-01 00:00:00',
            'date_to' => '2022-06-07 23:59:59'
        ];

        $this->json('POST', 'api/trips', $tripData, ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
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

    public function testCreateTripNonAlphaNumSpaceOrigin()
    {
        $this->seed();

        $token = $this->getLoginToken();

        $tripData = [
            'title' => 'Trip To Singapore',
            'description' => 'Trip To Singapore',
            'origin' => 'Indonesia #1',
            'destination' => 'Singapore',
            'date_from' => '2022-06-01 00:00:00',
            'date_to' => '2022-06-07 23:59:59'
        ];

        $this->json('POST', 'api/trips', $tripData, ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
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

    public function testCreateTripNonAlphaNumSpaceDestination()
    {
        $this->seed();

        $token = $this->getLoginToken();

        $tripData = [
            'title' => 'Trip To Singapore',
            'description' => 'Trip To Singapore',
            'origin' => 'Indonesia',
            'destination' => 'Singapore #1',
            'date_from' => '2022-06-01 00:00:00',
            'date_to' => '2022-06-07 23:59:59'
        ];

        $this->json('POST', 'api/trips', $tripData, ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
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

    public function testCreateTripDateFromInThePast()
    {
        $this->seed();

        $token = $this->getLoginToken();

        $tripData = [
            'title' => 'Trip To Singapore',
            'description' => 'Trip To Singapore',
            'origin' => 'Indonesia',
            'destination' => 'Singapore',
            'date_from' => '2022-05-01 00:00:00',
            'date_to' => '2022-06-07 23:59:59'
        ];

        $this->json('POST', 'api/trips', $tripData, ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
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

    public function testCreateTripDateToBeforeDateFrom()
    {
        $this->seed();

        $token = $this->getLoginToken();

        $tripData = [
            'title' => 'Trip To Singapore',
            'description' => 'Trip To Singapore',
            'origin' => 'Indonesia',
            'destination' => 'Singapore',
            'date_from' => '2022-06-01 00:00:00',
            'date_to' => '2022-05-07 23:59:59'
        ];

        $this->json('POST', 'api/trips', $tripData, ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
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

    public function testCreateTripSuccess()
    {
        $this->seed();

        $token = $this->getLoginToken();

        $tripData = [
            'title' => 'Trip To Singapore',
            'description' => 'Trip To Singapore',
            'origin' => 'Indonesia',
            'destination' => 'Singapore',
            'date_from' => '2022-06-01 00:00:00',
            'date_to' => '2022-06-07 23:59:59'
        ];

        $createTripResponse = $this->json('POST', 'api/trips', $tripData, ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
            ->assertStatus(200)
            ->decodeResponseJson();

        $createTripResponse->assertStructure([
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

        $trip = $createTripResponse['data']['trip'];

        $this->json('GET', 'api/trips', [], ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
            ->assertStatus(200)
            ->assertJsonFragment([
                'id' => $trip['id']
            ]);
    }
}
