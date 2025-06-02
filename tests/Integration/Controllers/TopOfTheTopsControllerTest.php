<?php

namespace Integration\Controllers;

use App\Services\AuthService;
use App\Services\RegisterService;
use Integration\BaseIntegrationTestCase;
use Integration\Traits\AuthenticationTestsTrait;
use Fakes\FakeTwitchClient;
use App\Interfaces\TwitchClientInterface;

class TopOfTheTopsControllerTest extends BaseIntegrationTestCase
{
    use AuthenticationTestsTrait;

    private array $authHeaders = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(TwitchClientInterface::class, new FakeTwitchClient());

        $apiKey = app(RegisterService::class)
            ->registerUser('tops@example.com')
            ->getData(true)['api_key'];

        $token = app(AuthService::class)
            ->createAccessToken('tops@example.com', $apiKey);

        $this->authHeaders = ['Authorization' => "Bearer $token"];
    }

    protected function getProtectedUrl(): string
    {
        return '/analytics/topsofthetops';
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
        $this->get('/analytics/topsofthetops', $this->authHeaders);

        $this->seeStatusCode(200)
            ->seeJsonStructure([
                '*' => [
                    'game_id',
                    'game_name',
                    'user_name',
                    'total_videos',
                    'total_views',
                    'most_viewed_title',
                    'most_viewed_views',
                    'most_viewed_duration',
                    'most_viewed_created_at',
                ],
            ]);
    }
}
