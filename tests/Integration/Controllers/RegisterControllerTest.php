<?php

namespace Integration\Controllers;

use Integration\BaseIntegrationTestCase;

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

        $responseData = json_decode($this->response->getContent(), true);
        $this->assertEquals(32, strlen($responseData['api_key']));
    }

    /** @test */
    public function registeringSameEmailReturnsDifferentApiKey()
    {
        $testEmail = 'user@example.com';

        $this->post('/register', ['email' => $testEmail]);
        $initialApiKey = json_decode($this->response->getContent(), true)['api_key'];

        $this->post('/register', ['email' => $testEmail]);
        $regeneratedApiKey = json_decode($this->response->getContent(), true)['api_key'];

        $this->assertNotEquals($initialApiKey, $regeneratedApiKey);
    }
}
