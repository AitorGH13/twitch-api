<?php

namespace Tests\Controllers;

use App\Services\AuthService;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\TestCase;
use App\Services\RegisterService;
use Tests\Traits\AuthenticationTestsTrait;

class TopOfTheTopsControllerTest extends TestCase
{
    use DatabaseMigrations;
    use AuthenticationTestsTrait;

    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    protected function getProtectedUrl(): string
    {
        return '/analytics/topsofthetops';
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
    public function invalidSinceParameterReturns400()
    {
        $this->get(
            '/analytics/topsofthetops?sing=2',
            $this->authHeaders
        );
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'since' parameter."]);
    }

    /** @test */
    public function emptySinceValueReturns400()
    {
        $this->get(
            '/analytics/topsofthetops?since=',
            $this->authHeaders
        );
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'since' parameter."]);
    }

    /** @test */
    public function nonNumericSinceValueReturns400()
    {
        $this->get(
            '/analytics/topsofthetops?since=a',
            $this->authHeaders
        );
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'since' parameter."]);
    }

    /** @test */
    public function negativeSinceValueReturns400()
    {
        $this->get(
            '/analytics/topsofthetops?since=-2',
            $this->authHeaders
        );
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'since' parameter."]);
    }

    /** @test */
    public function validRequestReturnsTopOfTheTopsList()
    {
        $this->get(
            $this->getProtectedUrl(),
            $this->authHeaders
        );

        $this->seeStatusCode(200)
            ->seeJsonStructure([
                [
                    'game_id',
                    'game_name',
                    'user_name',
                    'total_videos',
                    'total_views',
                    'most_viewed_title',
                    'most_viewed_views',
                    'most_viewed_duration',
                    'most_viewed_created_at'
                ]
            ]);
    }
}
