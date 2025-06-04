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
        $mockRegisterService = $this->mock(RegisterService::class);
        $testEmail = 'test@testing.com';
        $expectedApiKey = 'abcdef';

        $mockRegisterService->shouldReceive('registerUser')
            ->once()->with($testEmail)
            ->andReturn(new JsonResponse(['api_key' => $expectedApiKey]));

        $mockTokenService = $this->mock(TokenService::class);

        $authService = new AuthService($mockRegisterService, $mockTokenService);

        $returnedApiKey = $authService->registerEmail($testEmail);

        $this->assertSame($expectedApiKey, $returnedApiKey);
    }

    /** @test */
    public function createAccessTokenReturnsTheTokenString()
    {
        $mockRegisterService = $this->mock(RegisterService::class);
        $mockTokenService = $this->mock(TokenService::class);

        $testEmail = 'test@testing.com';
        $testApiKey = 'apikey';
        $expectedToken = 'xyz';

        $mockTokenService->shouldReceive('createToken')
            ->once()
            ->with($testEmail, $testApiKey)
            ->andReturn(new JsonResponse(['token' => $expectedToken]));

        $authService = new AuthService($mockRegisterService, $mockTokenService);

        $returnedToken = $authService->createAccessToken($testEmail, $testApiKey);

        $this->assertSame($expectedToken, $returnedToken);
    }

    /** @test */
    public function createAccessTokenReturnsInvalidApiKeyException()
    {
        $mockRegisterService = $this->mock(RegisterService::class);
        $mockTokenService = $this->mock(TokenService::class);

        $testEmail = 'test@testing.com';
        $invalidApiKey = 'wrongApiKey';

        $mockTokenService->shouldReceive('createToken')
            ->once()
            ->andThrow(InvalidApiKeyException::class);

        $authService = new AuthService($mockRegisterService, $mockTokenService);

        $this->expectException(InvalidApiKeyException::class);
        $authService->createAccessToken($testEmail, $invalidApiKey);
    }

    /** @test */
    public function validateAccessTokenIsASimpleProxy()
    {
        $mockRegisterService = $this->mock(RegisterService::class);
        $mockTokenService = $this->mock(TokenService::class);

        $testAccessToken = 'token123';

        $mockTokenService->shouldReceive('validateAccessToken')
            ->once()->with($testAccessToken)->andReturnTrue();

        $authService = new AuthService($mockRegisterService, $mockTokenService);

        $this->assertTrue($authService->validateAccessToken($testAccessToken));
    }
}
