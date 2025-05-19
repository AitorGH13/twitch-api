<?php // tests/Controllers/UserControllerTest.php

namespace Tests\Controllers;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\TestCase;
use App\Services\RegisterService;
use App\Services\AuthService;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function createApplication()
    {
        return require __DIR__.'/../../bootstrap/app.php';
    }

    /** @test */
    public function no_token_returns_401()
    {
        $this->get(
            '/analytics/user?id=1234',
            []
        );

        $this->seeStatusCode(401)
            ->seeJsonEquals(['error'=>'Unauthorized. Twitch access token is invalid or has expired.']);
    }

    /** @test */
    public function missing_id_returns_400()
    {
        // genera un token válido
        $apiKey = app(RegisterService::class)->registerUser('u@t.com')->getData(true)['api_key'];
        $token  = app(AuthService::class)->createAccessToken('u@t.com', $apiKey);

        $this->get(
            '/analytics/user',
            ['Authorization' => "Bearer {$token}"]
        );
      
        $this->seeStatusCode(400)
            ->seeJsonEquals(['error'=>"Invalid or missing 'id' parameter."]);
    }

    /** @test */
    public function not_found_returns_404()
    {
        $apiKey = app(RegisterService::class)->registerUser('u@x.com')->getData(true)['api_key'];
        $token  = app(AuthService::class)->createAccessToken('u@x.com', $apiKey);

        $this->get(
            '/analytics/user?id=9999',
            ['Authorization' => "Bearer {$token}"]
        );
      
        $this->seeStatusCode(404)
            ->seeJsonEquals(['error'=>'User not found.']);
    }

    /** @test */
    public function valid_request_returns_user_structure()
    {
        $apiKey = app(RegisterService::class)->registerUser('u@y.com')->getData(true)['api_key'];
        $token  = app(AuthService::class)->createAccessToken('u@y.com', $apiKey);

        $this->get(
            '/analytics/user?id=42',
            ['Authorization' => "Bearer {$token}"]
        );

        $this->seeStatusCode(200)
            ->seeJsonStructure([
                'id','login','display_name','type','broadcaster_type',
                'description','profile_image_url','offline_image_url',
                'view_count','created_at'
            ]);
    }
}
