<?php

namespace Unit\Managers;

use App\Domain\Token;
use App\Interfaces\TokenRepositoryInterface;
use App\Manager\TokenManager;
use App\Interfaces\UserRepositoryInterface;
use App\Support\TokenGenerator;
use Mockery;
use Unit\BaseUnitTestCase;

class TokenManagerTest extends BaseUnitTestCase
{
    /**
     * @test
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function provideTokenGeneratesNewTokenWith32Characters()
    {
        $testUserId = 1;
        $expectedTokenLength = 32;

        $mockUserRepository = $this->mock(UserRepositoryInterface::class);
        $mockTokenRepository = $this->mock(TokenRepositoryInterface::class);

        $mockTokenRepository->shouldReceive('findActiveByUserId')
            ->once()
            ->with($testUserId)
            ->andReturn(null);

        $mockTokenRepository->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Token::class));

        $realTokenGenerator = new TokenGenerator();
        $tokenManager = new TokenManager(
            $mockUserRepository,
            $mockTokenRepository,
            $realTokenGenerator
        );

        $generatedToken = $tokenManager->provideToken($testUserId);

        $this->assertInstanceOf(Token::class, $generatedToken);
        $this->assertEquals($expectedTokenLength, strlen($generatedToken->value));
        $this->assertFalse($generatedToken->isExpired());
    }
}
