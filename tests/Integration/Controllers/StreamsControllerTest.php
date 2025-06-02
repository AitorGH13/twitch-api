<?php

namespace Integration\Controllers;

use App\Services\AuthService;
use App\Services\RegisterService;
use Integration\BaseIntegrationTestCase;
use Integration\Traits\AuthenticationTestsTrait;

class StreamsControllerTest extends BaseIntegrationTestCase
{
    use AuthenticationTestsTrait;

    private array $authHeaders = [];

    protected function setUp(): void
    {
        parent::setUp();

        $apiKey = app(RegisterService::class)
            ->registerUser('streams@test.com')
            ->getData(true)['api_key'];

        $token = app(AuthService::class)
            ->createAccessToken('streams@test.com', $apiKey);

        $this->authHeaders = ['Authorization' => "Bearer $token"];
    }

    protected function getProtectedUrl(): string
    {
        return 'analytics/streams?limit=3';
    }

    /** @test */
    public function validRequestReturnsStreamsList()
    {
        $this->get('analytics/streams?limit=3', $this->authHeaders);

        $this->seeStatusCode(200)
            ->seeJsonEquals([
                ['title' => 'Title of Stream 1', 'user_name' => 'User1'],
                ['title' => 'Title of Stream 2', 'user_name' => 'User2'],
                ['title' => 'Title of Stream 3', 'user_name' => 'User3'],
            ]);
    }
}
