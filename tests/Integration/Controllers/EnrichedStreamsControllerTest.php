<?php

namespace Integration\Controllers;

use App\Interfaces\TwitchClientInterface;
use App\Services\AuthService;
use App\Services\RegisterService;
use Fakes\FakeTwitchClient;
use Integration\BaseIntegrationTestCase;
use Integration\Traits\AuthenticationTestsTrait;

class EnrichedStreamsControllerTest extends BaseIntegrationTestCase
{
    use AuthenticationTestsTrait;

    private array $authHeaders = [];

    protected function setUp(): void
    {
        parent::setUp();

        $apiKey = app(RegisterService::class)
            ->registerUser('enriched@example.com')
            ->getData(true)['api_key'];

        $token  = app(AuthService::class)
            ->createAccessToken('enriched@example.com', $apiKey);

        $this->authHeaders = ['Authorization' => "Bearer $token"];
    }

    protected function getProtectedUrl(): string
    {
        return 'analytics/streams/enriched?limit=3';
    }

    /** @test */
    public function missingLimitParameterReturns400()
    {
        $this->get('analytics/streams/enriched', $this->authHeaders);

        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'limit' parameter."]);
    }

    /** @test */
    public function invalidLimitParameterReturns400()
    {
        $this->get('analytics/streams/enriched?lim=3', $this->authHeaders);

        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'limit' parameter."]);
    }

    /** @test */
    public function emptyLimitValueReturns400()
    {
        $this->get('analytics/streams/enriched?limit=', $this->authHeaders);

        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'limit' parameter."]);
    }

    /** @test */
    public function limitValueOfZeroReturns400()
    {
        $this->get('analytics/streams/enriched?limit=0', $this->authHeaders);

        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'limit' parameter."]);
    }

    /** @test */
    public function negativeLimitValueReturns400()
    {
        $this->get('analytics/streams/enriched?limit=-3', $this->authHeaders);

        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'limit' parameter."]);
    }

    /** @test */
    public function nonNumericLimitValueReturns400()
    {
        $this->get('analytics/streams/enriched?limit=abc', $this->authHeaders);

        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'limit' parameter."]);
    }

    /** @test */
    public function validRequestReturnsEnrichedStreamsList()
    {
        $this->get('analytics/streams/enriched?limit=3', $this->authHeaders);

        $this->seeStatusCode(200)
            ->seeJsonStructure([
                '*' => [
                    'stream_id',
                    'user_id',
                    'user_name',
                    'viewer_count',
                    'title',
                    'user_display_name',
                    'profile_image_url',
                ],
            ]);
    }
}
