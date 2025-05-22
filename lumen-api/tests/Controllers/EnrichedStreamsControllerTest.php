<?php

namespace Tests\Controllers;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\TestCase;
use App\Services\RegisterService;
use App\Services\AuthService;
use Tests\Traits\AuthenticationTestsTrait;

class EnrichedStreamsControllerTest extends TestCase
{
    use DatabaseMigrations;
    use AuthenticationTestsTrait;

    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    protected function getProtectedUrl(): string
    {
        return '/analytics/streams/enriched?limit=3';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $apiKey = app(RegisterService::class)
            ->registerUser('user@example.com')
            ->getData(true)['api_key'];

        $token = app(AuthService::class)
            ->createAccessToken('user@example.com', $apiKey);

        $this->authHeaders = ['Authorization' => "Bearer $token"];
    }

    /** @test */
    public function missingLimitParameterReturns400()
    {
        $this->get(
            '/analytics/streams/enriched',
            $this->authHeaders
        );
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'limit' parameter."]);
    }

    /** @test */
    public function invalidLimitParameterReturns400()
    {
        $this->get(
            '/analytics/streams/enriched?lim=3',
            $this->authHeaders
        );
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'limit' parameter."]);
    }

    /** @test */
    public function emptyLimitValueReturns400()
    {
        $this->get(
            '/analytics/streams/enriched?limit=',
            $this->authHeaders
        );
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'limit' parameter."]);
    }

    /** @test */
    public function limitValueOfZeroReturns400()
    {
        $this->get(
            '/analytics/streams/enriched?limit=0',
            $this->authHeaders
        );
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'limit' parameter."]);
    }

    /** @test */
    public function negativeLimitValueReturns400()
    {
        $this->get(
            '/analytics/streams/enriched?limit=-1',
            $this->authHeaders
        );
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'limit' parameter."]);
    }

    /** @test */
    public function nonNumericLimitValueReturns400()
    {
        $this->get(
            '/analytics/streams/enriched?limit=a',
            $this->authHeaders
        );
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error' => "Invalid 'limit' parameter."]);
    }

    /** @test */
    public function validRequestReturnsEnrichedStreamsList()
    {
        $this->get(
            $this->getProtectedUrl(),
            $this->authHeaders
        );

        $this->seeStatusCode(200)
            ->seeJsonEquals([
                [
                    'stream_id'         => '1001',
                    'user_id'           => '2001',
                    'user_name'         => 'TopStreamer1',
                    'viewer_count'      => 1000,
                    'title'             => 'Epic Gaming Session 1',
                    'user_display_name' => 'Display 1',
                    'profile_image_url' => 'https://example.com/profile.png'
                ],
                [
                    'stream_id'         => '1002',
                    'user_id'           => '2002',
                    'user_name'         => 'TopStreamer2',
                    'viewer_count'      => 2000,
                    'title'             => 'Epic Gaming Session 2',
                    'user_display_name' => 'Display 2',
                    'profile_image_url' => 'https://example.com/profile.png'
                ],
                [
                    'stream_id'         => '1003',
                    'user_id'           => '2003',
                    'user_name'         => 'TopStreamer3',
                    'viewer_count'      => 3000,
                    'title'             => 'Epic Gaming Session 3',
                    'user_display_name' => 'Display 3',
                    'profile_image_url' => 'https://example.com/profile.png'
                ],
            ]);
    }
}
