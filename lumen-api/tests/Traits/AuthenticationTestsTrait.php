<?php

namespace Tests\Traits;

use App\Services\RegisterService;
use App\Services\AuthService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait AuthenticationTestsTrait
{
    abstract protected function getProtectedUrl(): string;

    protected function createValidToken(string $email = 'u@e.com'): string
    {
        $apiKey = app(RegisterService::class)
            ->registerUser($email)
            ->getData(true)['api_key'];

        return app(AuthService::class)
            ->createAccessToken($email, $apiKey);
    }

    /** @test */
    public function missingAuthorizationHeaderReturns401(): void
    {
        $this->get($this->getProtectedUrl());
        $this->seeStatusCode(401)
            ->seeJsonEquals(['error' => 'Unauthorized. Twitch access token is invalid or has expired.']);
    }

    /** @test */
    public function emptyAuthorizationTokenReturns401(): void
    {
        $this->get(
            $this->getProtectedUrl(),
            ['Authorization' => 'Bearer ']
        );
        $this->seeStatusCode(401)
            ->seeJsonEquals(['error' => 'Unauthorized. Twitch access token is invalid or has expired.']);
    }

    /** @test */
    public function invalidAuthorizationTokenReturns401(): void
    {
        $this->get(
            $this->getProtectedUrl(),
            ['Authorization' => 'Bearer thisIsAnInvalidToken123456']
        );
        $this->seeStatusCode(401)
            ->seeJsonEquals(['error' => 'Unauthorized. Twitch access token is invalid or has expired.']);
    }

    /** @test */
    public function expiredAuthorizationTokenReturns401(): void
    {
        $token = $this->createValidToken();

        DB::table('sessions')
            ->where('token', $token)
            ->update(['expires_at' => Carbon::now()->subHour()->toDateTimeString()]);

        $this->get(
            $this->getProtectedUrl(),
            ['Authorization' => "Bearer $token"]
        );

        $this->seeStatusCode(401)
            ->seeJsonEquals(['error' => 'Unauthorized. Twitch access token is invalid or has expired.']);
    }
}
