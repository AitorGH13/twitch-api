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
        $manager = $this->mock(TokenManager::class);

        $manager->shouldReceive('checkUser')
            ->once()->with('test@testing.com', 'badApiKey')
            ->andReturnNull();

        $manager->shouldNotReceive('provideToken');

        $service = new TokenService($manager);

        $this->expectException(InvalidApiKeyException::class);
        $service->createToken('test@testing.com', 'badApiKey');
    }

    /** @test */
    public function validCredentialsReturnsJsonWithToken()
    {
        $manager = $this->mock(TokenManager::class);

        $manager->shouldReceive('checkUser')
            ->once()->with('test@testing.com', 'goodApiKey')
            ->andReturn(7);

        $tokenObj = new Token(
            'token123',
            7,
            new DateTimeImmutable('+1 hour')
        );

        $manager->shouldReceive('provideToken')
            ->once()->with(7)
            ->andReturn($tokenObj);

        $service  = new TokenService($manager);
        $response = $service->createToken('test@testing.com', 'goodApiKey');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['token' => 'token123'], $response->getData(true));
    }

    /** @test */
    public function validateAccessTokenIsASimpleProxy()
    {
        $manager = $this->mock(TokenManager::class);
        $manager->shouldReceive('tokenIsActive')
            ->once()->with('validToken')->andReturnTrue();

        $service = new TokenService($manager);

        $this->assertTrue($service->validateAccessToken('validToken'));
    }
}
