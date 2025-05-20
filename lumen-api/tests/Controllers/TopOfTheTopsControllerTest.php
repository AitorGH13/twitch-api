<?php // tests/Controllers/TopOfTheTopsControllerTest.php

namespace Tests\Controllers;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\TestCase;
use App\Repository\TopOfTheTopsRepository;
use App\Services\TopOfTheTopsService;
use App\Services\RegisterService;
use App\Services\TokenService;
use App\Services\AuthService;

class TopOfTheTopsControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function createApplication()
    {
        return require __DIR__.'/../../bootstrap/app.php';
    }

    /** @test */
    public function no_token_returns_401()
    {
        $this->get('/analytics/topsofthetops');
        $this->seeStatusCode(401)
            ->seeJsonEquals(['error'=>'Unauthorized. Twitch access token is invalid or has expired.']);
    }

    /** @test */
    public function invalid_token_returns_401()
    {
        $apiKey = app(RegisterService::class)->registerUser('u@v.com')->getData(true)['api_key'];

        $this->get(
            '/analytics/topsofthetops?since=abc',
            ['Authorization' => "Bearer abcd1234"]
        );
        $this->seeStatusCode(401)
            ->seeJsonEquals(['error'=>'Unauthorized. Twitch access token is invalid or has expired.']);
    }

    /** @test */
    public function invalid_since_parameter_returns_400()
    {
        $apiKey = app(RegisterService::class)->registerUser('u@v.com')->getData(true)['api_key'];
        $token  = app(TokenService::class)->createToken('u@v.com', $apiKey)->getData(true)['token'];

        $this->get(
            '/analytics/topsofthetops?since=abc',
            ['Authorization' => "Bearer {$token}"]
        );
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error'=>"Invalid 'since' parameter."]);
    }

    /** @test */
    public function invalid_since_returns_400()
    {
        $apiKey = app(RegisterService::class)->registerUser('u@v.com')->getData(true)['api_key'];
        $token  = app(TokenService::class)->createToken('u@v.com', $apiKey)->getData(true)['token'];

        $this->get(
            '/analytics/topsofthetops?sinc=1',
            ['Authorization' => "Bearer {$token}"]
        );
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error'=>"Invalid 'since' parameter."]);
    }

    /** @test */
    public function valid_request_returns_structure()
    {
        $apiKey = app(RegisterService::class)->registerUser('u@w.com')->getData(true)['api_key'];
        $token  = app(TokenService::class)->createToken('u@w.com', $apiKey)->getData(true)['token'];

        $this->get(
            '/analytics/topsofthetops',
            ['Authorization' => "Bearer {$token}"]
        );

        $this->seeStatusCode(200)
            ->seeJsonStructure([
                ['game_id','game_name','user_name','total_videos','total_views',
                    'most_viewed_title','most_viewed_views','most_viewed_duration','most_viewed_created_at']
            ]);
    }
}
