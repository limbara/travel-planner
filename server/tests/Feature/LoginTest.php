<?php

namespace Tests\Feature;

class LoginTest extends BaseTest
{
    public function testLoginRequiresEmailAndPassword()
    {
        $this->json('POST', 'api/auth/login', [], ['Accept' => 'application/json'])
            ->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.']
                ]
            ])
            ->assertJsonStructure([
                'error_code',
                'error_message',
                'errors'
            ]);
    }

    public function testLoginInvalidEmail()
    {
        $this->seed();

        $loginData = [
            'email' => 'Nico@example.net',
            'password' => 'password'
        ];

        $this->json('POST', 'api/auth/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'error_code' => 401,
                'error_message' => 'invalid email or password'
            ]);
    }

    public function testLoginInvalidPassword()
    {
        $this->seed();

        $loginData = [
            'email' => 'nico@example.com',
            'password' => 'wrong password'
        ];

        $this->json('POST', 'api/auth/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'error_code' => 401,
                'error_message' => 'invalid email or password'
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed();

        $loginData = [
            'email' => 'nico@example.com',
            'password' => 'password'
        ];

        $this->json('POST', 'api/auth/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'token'
                ]
            ]);
    }
}
