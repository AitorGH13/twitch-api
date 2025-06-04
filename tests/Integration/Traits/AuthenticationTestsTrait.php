<?php

namespace Integration\Traits;

use App\Services\AuthService;
use App\Services\RegisterService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        $expiredToken = $this->createValidToken();

        DB::table('sessions')
            ->where('token', $expiredToken)
            ->update(['expires_at' => Carbon::now()->subHour()->toDateTimeString()]);

        $this->get(
            $this->getProtectedUrl(),
            ['Authorization' => "Bearer $expiredToken"]
        );

        $this->seeStatusCode(401)
            ->seeJsonEquals(['error' => 'Unauthorized. Twitch access token is invalid or has expired.']);
    }
}
