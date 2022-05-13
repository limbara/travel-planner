<?php

namespace Tests\Feature;

use App\Models\User;

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

        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $this->json('GET', 'api/auth/user', [], ['Accept' => 'application/json'])
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
