<?php

namespace Unit\Services;

use App\Services\AuthService;
use App\Services\RegisterService;
use App\Services\TokenService;
use App\Exceptions\InvalidApiKeyException;
use Illuminate\Http\JsonResponse;
use Random\RandomException;
use Unit\BaseUnitTestCase;

class AuthServiceTest extends BaseUnitTestCase
{
    /** @test
     * @throws RandomException
     */
    public function registerEmailReturnsTheApiKeyString()
    {
        $register = $this->mock(RegisterService::class);
        $register->shouldReceive('registerUser')
            ->once()->with('test@testing.com')
            ->andReturn(new JsonResponse(['api_key' => 'abcdef']));

        $tokenService = $this->mock(TokenService::class);

        $service = new AuthService($register, $tokenService);

        $key = $service->registerEmail('test@testing.com');

        $this->assertSame('abcdef', $key);
    }

    /** @test */
    public function createAccessTokenReturnsTheTokenString()
    {
        $register     = $this->mock(RegisterService::class);
        $tokenService = $this->mock(TokenService::class);

        $tokenService->shouldReceive('createToken')
            ->once()
            ->with('test@testing.com', 'apikey')
            ->andReturn(new JsonResponse(['token' => 'xyz']));

        $service = new AuthService($register, $tokenService);

        $token = $service->createAccessToken('test@testing.com', 'apikey');

        $this->assertSame('xyz', $token);
    }

    /** @test */
    public function createAccessTokenPropagatesInvalidApiKeyException()
    {
        $register     = $this->mock(RegisterService::class);
        $tokenService = $this->mock(TokenService::class);

        $tokenService->shouldReceive('createToken')
            ->once()
            ->andThrow(InvalidApiKeyException::class);

        $service = new AuthService($register, $tokenService);

        $this->expectException(InvalidApiKeyException::class);
        $service->createAccessToken('test@testing.com', 'wrongApiKey');
    }

    /** @test */
    public function validateAccessTokenIsASimpleProxy()
    {
        $register     = $this->mock(RegisterService::class);
        $tokenService = $this->mock(TokenService::class);

        $tokenService->shouldReceive('validateAccessToken')
            ->once()->with('token123')->andReturnTrue();

        $service = new AuthService($register, $tokenService);

        $this->assertTrue($service->validateAccessToken('token123'));
    }
}
