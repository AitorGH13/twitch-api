<?php // tests/Controllers/StreamsControllerTest.php

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
        return require __DIR__.'/../../bootstrap/app.php';
    }

    /** @test */
    public function no_token_returns_401()
    {
        $this->get('/analytics/streams');
        $this->seeStatusCode(401)
            ->seeJsonEquals([
                'error' => 'Unauthorized. Twitch access token is invalid or has expired.'
            ]);
    }

    /** @test */
    public function valid_request_returns_streams_list()
    {
        // generar usuario y token válidos
        $apiKey = app(RegisterService::class)
            ->registerUser('u@s.com')
            ->getData(true)['api_key'];
        $token  = app(AuthService::class)
            ->createAccessToken('u@s.com', $apiKey);

        $this->get("/analytics/streams?token={$token}");
        $this->seeStatusCode(200)
            ->seeJsonEquals([
                ['title'=>'Title of Stream 1','user_name'=>'User1'],
                ['title'=>'Title of Stream 2','user_name'=>'User2'],
                ['title'=>'Title of Stream 3','user_name'=>'User3'],
            ]);
    }
}
