<?php

namespace Integration\Controllers;

use App\Services\AuthService;
use App\Services\RegisterService;
use Integration\BaseIntegrationTestCase;
use Integration\Traits\AuthenticationTestsTrait;

class StreamerControllerTest extends BaseIntegrationTestCase
{
    use AuthenticationTestsTrait;

    private array $authHeaders = [];

    protected function setUp(): void
    {
        parent::setUp();

        $apiKey = app(RegisterService::class)
            ->registerUser('streamer@test.com')
            ->getData(true)['api_key'];

        $token = app(AuthService::class)
            ->createAccessToken('streamer@test.com', $apiKey);

        $this->authHeaders = ['Authorization' => "Bearer $token"];
    }

    protected function getProtectedUrl(): string
    {
        return '/analytics/user?id=1';
    }

    /** @test */
    public function missingIdParameterReturns400()
    {
        $this->get(
            '/analytics/user',
            $this->authHeaders
        );

        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid or missing 'id' parameter."]);
    }

    /** @test */
    public function invalidIdParameterReturns400()
    {
        $this->get(
            '/analytics/user?idd=1',
            $this->authHeaders
        );

        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid or missing 'id' parameter."]);
    }

    /** @test */
    public function emptyIdValueReturns400()
    {
        $this->get(
            '/analytics/user?id=',
            $this->authHeaders
        );

        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid or missing 'id' parameter."]);
    }

    /** @test */
    public function nonNumericIdValueReturns400()
    {
        $this->get(
            '/analytics/user?id=abc',
            $this->authHeaders
        );

        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid or missing 'id' parameter."]);
    }

    /** @test */
    public function negativeIdValueReturns400()
    {
        $this->get(
            '/analytics/user?id=-1',
            $this->authHeaders
        );

        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid or missing 'id' parameter."]);
    }

    /** @test */
    public function notFoundIdReturns404()
    {
        $this->get(
            '/analytics/user?id=9999',
            $this->authHeaders
        );

        $this->seeStatusCode(404)
            ->seeJsonEquals(['error' => 'User not found.']);
    }

    /** @test */
    public function validRequestReturnsStreamerInfo()
    {
        $this->get('/analytics/user?id=1', $this->authHeaders);

        $this->seeStatusCode(200)
            ->seeJsonStructure([
                'id',
                'login',
                'display_name',
                'type',
                'broadcaster_type',
                'description',
                'profile_image_url',
                'offline_image_url',
                'view_count',
                'created_at'
            ]);
    }
}
