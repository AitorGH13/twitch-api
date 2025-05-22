<?php

namespace Tests\Controllers;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\TestCase;
use App\Services\RegisterService;
use App\Services\AuthService;

class StreamsControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $apiKey = app(RegisterService::class)
            ->registerUser('test@test.com')
            ->getData(true)['api_key'];

        $token = app(AuthService::class)
            ->createAccessToken('test@test.com', $apiKey);

        $this->authHeaders = ['Authorization' => "Bearer $token"];
    }

    /** @test */
    public function noTokenReturns401()
    {
        $this->get('/analytics/streams');
        $this->seeStatusCode(401)
            ->seeJsonEquals([
                'error' => 'Unauthorized. Twitch access token is invalid or has expired.'
            ]);
    }

    /** @test */
    public function validRequestReturnsStreamsList()
    {
        $this->get(
            '/analytics/streams',
            $this->authHeaders
        );

        $this->seeStatusCode(200)
            ->seeJsonEquals([
                ['title' => 'Title of Stream 1', 'user_name' => 'User1'],
                ['title' => 'Title of Stream 2', 'user_name' => 'User2'],
                ['title' => 'Title of Stream 3', 'user_name' => 'User3'],
            ]);
    }
}
