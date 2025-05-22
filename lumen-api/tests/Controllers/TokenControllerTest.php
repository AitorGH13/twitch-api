<?php

namespace Tests\Controllers;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\TestCase;
use App\Services\AuthService;

class TokenControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Define application setup.
     */
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    /** @test */
    public function tokenWithoutEmailReturns400()
    {
        $this->post('/token', ['api_key' => 'any']);
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The email is mandatory']);
    }

    /** @test */
    public function tokenWithoutApiKeyReturns400()
    {
        $this->post('/token', ['email' => 'user@example.com']);
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The api_key is mandatory']);
    }

    /** @test */
    public function tokenWithInvalidEmailReturns400()
    {
        $this->post('/token', ['email' => 'not_email@.com', 'api_key' => 'abc']);
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The email must be a valid email address']);
    }

    /** @test */
    public function tokenWithInvalidApiKeyReturns401()
    {
        $this->post('/token', [
            'email'   => 'user@example.com',
            'api_key' => 'abed1234'
        ]);
        $this->seeStatusCode(401)
            ->seeJsonEquals(['error' => 'Unauthorized. API access token is invalid.']);
    }

    /** @test */
    public function tokenWithValidCredentialsReturnsToken()
    {
        $email    = 'user@example.com';
        $validKey = app(AuthService::class)->registerEmail($email);

        $this->post('/token', [
            'email'   => $email,
            'api_key' => $validKey,
        ]);
        $this->seeStatusCode(200)
            ->seeJsonStructure(['token']);

        $body = json_decode($this->response->getContent(), true);
        $this->assertEquals(32, strlen($body['token']));
    }
}
