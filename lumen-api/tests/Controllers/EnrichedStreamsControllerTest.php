<?php // tests/Controllers/EnrichedStreamsControllerTest.php

namespace Tests\Controllers;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\TestCase;
use App\Services\RegisterService;
use App\Services\AuthService;

class EnrichedStreamsControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function createApplication()
    {
        return require __DIR__.'/../../bootstrap/app.php';
    }

    /** @test */
    public function no_token_returns_401()
    {
        $this->get('/analytics/streams/enriched?limit=3');
        $this->seeStatusCode(401)
            ->seeJsonEquals([
                'error' => 'Unauthorized. Twitch access token is invalid or has expired.'
            ]);
    }

    /** @test */
    public function invalid_limit_returns_400()
    {
        // creamos usuario y token válidos
        $apiKey = app(RegisterService::class)
            ->registerUser('u@e.com')
            ->getData(true)['api_key'];
        $token  = app(AuthService::class)
            ->createAccessToken('u@e.com', $apiKey);

        $this->get(
            '/analytics/streams/enriched?limit=0',
            ['Authorization' => "Bearer {$token}"]
        );
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error'=>"Invalid 'limit' parameter."]);
    }

    /** @test */
    public function valid_request_returns_enriched_streams()
    {
        $apiKey = app(RegisterService::class)
            ->registerUser('u@e2.com')
            ->getData(true)['api_key'];
        $token  = app(AuthService::class)
            ->createAccessToken('u@e2.com', $apiKey);

        $this->get(
            '/analytics/streams/enriched?limit=3',
            ['Authorization' => "Bearer {$token}"]
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
