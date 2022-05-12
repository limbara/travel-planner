<?php

namespace Tests\Feature;

class AuthUserTest extends BaseTest
{
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

        $token = $this->getLoginToken();

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
