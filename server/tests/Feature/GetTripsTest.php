<?php

namespace Tests\Feature;

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
        $this->seed();

        $token = $this->getLoginToken();

        $jsonResponse = $this->json('GET', 'api/trips', [], ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])->assertStatus(200)->decodeResponseJson();

        $jsonResponse->assertStructure([
            'message',
            'data' => [
                'trips'
            ]
        ]);
    }
}
