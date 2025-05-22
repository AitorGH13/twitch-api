<?php

namespace Tests\Traits;

trait AuthenticationTestsTrait
{
    abstract protected function getProtectedUrl(): string;

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
            ['Authorization' => 'Bearer thisIsAnInvalidToken1234567890']
        );
        $this->seeStatusCode(401)
            ->seeJsonEquals(['error' => 'Unauthorized. Twitch access token is invalid or has expired.']);
    }
}
