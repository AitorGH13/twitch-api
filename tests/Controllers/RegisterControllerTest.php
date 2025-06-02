<?php

namespace Tests\Controllers;

use Tests\BaseIntegrationTestCase;

class RegisterControllerTest extends BaseIntegrationTestCase
{
    /** @test */
    public function registerWithoutEmailReturns400()
    {
        $this->post('/register');
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The email is mandatory']);
    }

    /** @test */
    public function registerWithEmptyEmailReturns400()
    {
        $this->post('/register', ['email' => '']);
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The email is mandatory']);
    }

    /** @test */
    public function registerWithInvalidEmailReturns400()
    {
        $this->post('/register', ['email' => 'not_email@.com']);
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The email must be a valid email address']);
    }

    /** @test */
    public function registerWithValidEmailReturnsApiKey()
    {
        $this->post('/register', ['email' => 'user@example.com']);
        $this->seeStatusCode(200)
            ->seeJsonStructure(['api_key']);

        $body = json_decode($this->response->getContent(), true);
        $this->assertEquals(32, strlen($body['api_key']));
    }

    /** @test */
    public function registeringSameEmailReturnsDifferentApiKey()
    {
        $email = 'user@example.com';

        $this->post('/register', ['email' => $email]);
        $firstApiKey = json_decode($this->response->getContent(), true)['api_key'];

        $this->post('/register', ['email' => $email]);
        $secondApiKey = json_decode($this->response->getContent(), true)['api_key'];

        $this->assertNotEquals($firstApiKey, $secondApiKey);
    }
}
