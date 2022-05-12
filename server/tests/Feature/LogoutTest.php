<?php

namespace Tests\Feature;

class LogoutTest extends BaseTest
{
    public function testLogoutWithoutToken()
    {
        $this->json('POST', 'api/auth/logout', [], ['Accept' => 'application/json'])
            ->assertStatus(401)
            ->assertJson([
                'error_code' => 401,
                'error_message' => 'Unauthenticated.'
            ]);
    }

    public function testLoggedOutProperly()
    {
        $this->seed();

        $token = $this->getLoginToken();
        
        $this->json('POST', 'api/auth/logout', [], ['Accept' => 'application/json', 'Authorization' => "Bearer $token"]);

        // laravel is caching user in guards https://laracasts.com/discuss/channels/testing/tdd-with-sanctum-issue-with-user-logout-case
        $this->app->get('auth')->forgetGuards();

        $this->json('GET', 'api/auth/user', [], ['Accept' => 'application/json', 'Authorization' => "Bearer $token"])->assertStatus(401);
    }
}
