<?php // tests/Controllers/TokenControllerTest.php

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
        return require __DIR__.'/../../bootstrap/app.php';
    }

    /** @test */
    public function token_without_email_returns_400()
    {
        $this->post('/token', ['api_key' => 'any']);
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The email is mandatory']);
    }

    /** @test */
    public function token_without_api_key_returns_400()
    {
        $this->post('/token', ['email' => 'user@example.com']);
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The api_key is mandatory']);
    }

    /** @test */
    public function token_with_invalid_email_returns_400()
    {
        $this->post('/token', ['email' => 'not-an-email', 'api_key' => 'abc']);
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The email must be a valid email address']);
    }

    /** @test */
    public function token_with_invalid_api_key_returns_401()
    {
        // primero registro un email para generar un api_key válido
        $validApiKey = app(AuthService::class)->registerEmail('user@example.com');

        // ahora pruebo con otro api_key
        $this->post('/token', [
            'email'   => 'user@example.com',
            'api_key' => 'deadbeef'
        ]);
        $this->seeStatusCode(401)
            ->seeJsonEquals(['error' => 'Unauthorized. API access token is invalid.']);
    }

    /** @test */
    public function token_with_valid_credentials_returns_token()
    {
        $email      = 'user@example.com';
        $validKey   = app(AuthService::class)->registerEmail($email);

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
