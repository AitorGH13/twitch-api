<?php

namespace Tests\Controllers;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\TestCase;

class RegisterControllerTest extends TestCase
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
    public function registerWithoutEmailReturns400()
    {
        $this->post('/register');
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => 'The email is mandatory']);
    }

    /** @test */
    public function registerWithInvalidEmailReturns400()
    {
        $this->post('/register', ['email' => 'foo@.com']);
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
}
