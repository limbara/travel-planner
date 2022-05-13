<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class BaseTest extends TestCase
{
    use RefreshDatabase;

    protected function getLoginToken(string $email = 'nico@example.com', string $password = 'password')
    {
        $loginData = [
            'email' => $email,
            'password' => $password
        ];

        $loginResponse = $this->json('POST', 'api/auth/login', $loginData, ['Accept' => 'application/json'])->decodeResponseJson();
        $token = $loginResponse['data']['token'] ?? '';

        return $token;
    }
}
