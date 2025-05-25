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

    protected function setUp(): void
    {
        parent::setUp();

        $email = 'user@example.com';
        $validKey = app(AuthService::class)->registerEmail($email);

        $this->validKey = $validKey;
        $this->validEmail   = $email;
    }

    /** @test */
    public function tokenWithoutEmailReturns400()
    {
        $this->post('/token', [
            'api_key' => $this->validKey,
        ]);
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The email is mandatory']);
    }

    /** @test */
    public function tokenWithoutApiKeyReturns400()
    {
        $this->post('/token', [
            'email' => $this->validEmail,
        ]);
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The api_key is mandatory']);
    }

    /** @test */
    public function tokenWithEmptyEmailReturns400()
    {
        $this->post('/token', [
            'email' => '',
            'api_key' => $this->validKey,
        ]);
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The email is mandatory']);
    }

    /** @test */
    public function tokenWithEmptyApiKeyReturns400()
    {
        $this->post('/token', [
            'email' => $this->validEmail,
            'api_key' => '',
        ]);
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The api_key is mandatory']);
    }

    /** @test */
    public function tokenWithInvalidEmailReturns400()
    {
        $this->post('/token', [
            'email' => 'not_email@.com',
            'api_key' => $this->validKey,
        ]);
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The email must be a valid email address']);
    }

    /** @test */
    public function tokenWithInvalidApiKeyReturns401()
    {
        $this->post('/token', [
            'email'   => $this->validEmail,
            'api_key' => 'abed1234'
        ]);
        $this->seeStatusCode(401)
            ->seeJsonEquals(['error' => 'Unauthorized. API access token is invalid.']);
    }

    /** @test */
    public function tokenWithValidCredentialsReturnsToken()
    {
        $this->post('/token', [
            'email'   => $this->validEmail,
            'api_key' => $this->validKey,
        ]);
        $this->seeStatusCode(200)
            ->seeJsonStructure(['token']);

        $body = json_decode($this->response->getContent(), true);
        $this->assertEquals(32, strlen($body['token']));
    }

    /** @test */
    public function tokenWithSameCredentialsReturnsSameToken()
    {
        $this->post('/token', [
            'email'   => $this->validEmail,
            'api_key' => $this->validKey,
        ]);
        $this->seeStatusCode(200);
        $firstBody = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('token', $firstBody);
        $firstToken = $firstBody['token'];

        $this->post('/token', [
            'email'   => $this->validEmail,
            'api_key' => $this->validKey,
        ]);
        $this->seeStatusCode(200);
        $secondBody = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('token', $secondBody);
        $secondToken = $secondBody['token'];

        $this->assertEquals($firstToken, $secondToken);
    }
}
