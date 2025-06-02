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
        $userId = 1;

        $userRepo = $this->mock(UserRepositoryInterface::class);

        $tokenRepo = $this->mock(TokenRepositoryInterface::class);
        $tokenRepo->shouldReceive('findActiveByUserId')
            ->once()
            ->with($userId)
            ->andReturn(null);

        $tokenRepo->shouldReceive('save')
            ->once()
            ->with(Mockery::type(Token::class));

        $generator = new TokenGenerator();
        $manager   = new TokenManager($userRepo, $tokenRepo, $generator);

        $token = $manager->provideToken($userId);

        $this->assertInstanceOf(Token::class, $token);
        $this->assertEquals(32, strlen($token->value));
        $this->assertFalse($token->isExpired());
    }
}
