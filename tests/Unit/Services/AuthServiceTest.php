<?php

namespace Unit\Services;

use App\Services\AuthService;
use App\Services\RegisterService;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Random\RandomException;
use Unit\BaseUnitTestCase;

class AuthServiceTest extends BaseUnitTestCase
{
    /** @test
     * @throws RandomException
     */
    public function registerEmailReturns32CharApiKey()
    {
        $register = $this->mock(RegisterService::class);
        $register->shouldReceive('registerUser')
            ->once()
            ->with('user@example.com')
            ->andReturn(new JsonResponse(['api_key' => str_repeat('A', 32)], 201));

        $tokenSrv = $this->mock(TokenService::class);

        $service = new AuthService($register, $tokenSrv);

        $apiKey = $service->registerEmail('user@example.com');

        $this->assertEquals(32, strlen($apiKey));
    }

    /** @test */
    public function createAccessTokenDelegatesToTokenService()
    {
        $register = $this->mock(RegisterService::class);

        $tokenSrv = $this->mock(TokenService::class);
        $tokenSrv->shouldReceive('createToken')
            ->once()
            ->with('user@example.com', 'valid-key')
            ->andReturn(new JsonResponse(['token' => str_repeat('B', 32)], 200));

        $service = new AuthService($register, $tokenSrv);

        $token = $service->createAccessToken('user@example.com', 'valid-key');

        $this->assertEquals(32, strlen($token));
    }
}
