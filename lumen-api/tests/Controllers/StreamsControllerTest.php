<?php

namespace Tests\Controllers;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\TestCase;
use App\Services\RegisterService;
use App\Services\AuthService;
use Tests\Traits\AuthenticationTestsTrait;

class StreamsControllerTest extends TestCase
{
    use DatabaseMigrations;
    use AuthenticationTestsTrait;

    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    protected function getProtectedUrl(): string
    {
        return '/analytics/streams';
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
    public function validRequestReturnsStreamsList()
    {
        $this->get(
            $this->getProtectedUrl(),
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
