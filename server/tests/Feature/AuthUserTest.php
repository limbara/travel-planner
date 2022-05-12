<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthUserTest extends TestCase
{
    use RefreshDatabase;

    public function testAuthUserWithoutToken()
    {
        $this->json('GET', 'api/auth/user', [], ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'error_code' => 401,
                'error_message' => 'Unauthenticated.'
            ]);
    }

    public function testAuthUserWithToken()
    {
        $this->seed();

        $loginData = [
            'email' => 'Nico@example.com',
            'password' => 'password'
        ];

        $loginResponse = $this->json('POST', 'api/auth/login', $loginData, ['Accept' => 'application/json'])->decodeResponseJson();
        $token = $loginResponse['data']['token'];

        $this->json('GET', 'api/auth/user', [], ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }
}
