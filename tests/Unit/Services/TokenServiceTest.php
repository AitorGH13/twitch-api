<?php

namespace Unit\Services;

use App\Exceptions\InvalidApiKeyException;
use App\Manager\TokenManager;
use App\Services\TokenService;
use App\Domain\Token;
use DateTimeImmutable;
use Unit\BaseUnitTestCase;

class TokenServiceTest extends BaseUnitTestCase
{
    /** @test */
    public function invalidCredentialsThrowsInvalidApiKeyException()
    {
        $mockTokenManager = $this->mock(TokenManager::class);
        $testEmail = 'test@testing.com';
        $invalidApiKey = 'badApiKey';

        $mockTokenManager->shouldReceive('checkUser')
            ->once()->with($testEmail, $invalidApiKey)
            ->andReturnNull();

        $mockTokenManager->shouldNotReceive('provideToken');

        $tokenService = new TokenService($mockTokenManager);

        $this->expectException(InvalidApiKeyException::class);
        $tokenService->createToken($testEmail, $invalidApiKey);
    }

    /** @test */
    public function validCredentialsReturnsJsonWithToken()
    {
        $mockTokenManager = $this->mock(TokenManager::class);
        $testEmail = 'test@testing.com';
        $validApiKey = 'goodApiKey';
        $testUserId = 7;
        $expectedTokenValue = 'token123';

        $mockTokenManager->shouldReceive('checkUser')
            ->once()->with($testEmail, $validApiKey)
            ->andReturn($testUserId);

        $tokenObject = new Token(
            $expectedTokenValue,
            $testUserId,
            new DateTimeImmutable('+1 hour')
        );

        $mockTokenManager->shouldReceive('provideToken')
            ->once()->with($testUserId)
            ->andReturn($tokenObject);

        $tokenService = new TokenService($mockTokenManager);
        $response = $tokenService->createToken($testEmail, $validApiKey);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['token' => $expectedTokenValue], $response->getData(true));
    }

    /** @test */
    public function validateAccessTokenIsASimpleProxy()
    {
        $mockTokenManager = $this->mock(TokenManager::class);
        $testAccessToken = 'validToken';

        $mockTokenManager->shouldReceive('tokenIsActive')
            ->once()->with($testAccessToken)->andReturnTrue();

        $tokenService = new TokenService($mockTokenManager);

        $this->assertTrue($tokenService->validateAccessToken($testAccessToken));
    }
}
