<?php

namespace Integration\Controllers;

use App\Services\AuthService;
use Integration\BaseIntegrationTestCase;

class TokenControllerTest extends BaseIntegrationTestCase
{
    private string $validKey;
    private string $validEmail;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validEmail = 'token@test.com';
        $this->validKey   = app(AuthService::class)->registerEmail($this->validEmail);
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
            'email' => $this->validEmail,
            'api_key' => 'abed1234'
        ]);
        $this->seeStatusCode(401)
            ->seeJsonEquals(['error' => 'Unauthorized. API access token is invalid.']);
    }

    /** @test */
    public function validCredentialsReturnToken()
    {
        $this->post('/token', [
            'email'   => $this->validEmail,
            'api_key' => $this->validKey,
        ]);

        $this->seeStatusCode(200)
            ->seeJsonStructure(['token']);

        $token = json_decode($this->response->getContent(), true)['token'];
        $this->assertEquals(32, strlen($token));
    }

    /** @test */
    public function tokenWithSameCredentialsReturnsSameToken()
    {
        $this->post('/token', [
            'email' => $this->validEmail,
            'api_key' => $this->validKey,
        ]);
        $this->seeStatusCode(200);
        $firstBody = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('token', $firstBody);
        $firstToken = $firstBody['token'];

        $this->post('/token', [
            'email' => $this->validEmail,
            'api_key' => $this->validKey,
        ]);
        $this->seeStatusCode(200);
        $secondBody = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('token', $secondBody);
        $secondToken = $secondBody['token'];

        $this->assertEquals($firstToken, $secondToken);
    }
}
