<?php

namespace App\Manager;

use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\TokenRepositoryInterface;
use App\Support\TokenGenerator;
use App\Domain\Token;
use DateTimeImmutable;
use Random\RandomException;

class TokenManager
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly TokenRepositoryInterface $tokens,
        private readonly TokenGenerator $generator,
    ) {
    }

    public function checkUser(string $email, string $apiKey): ?int
    {
        return $this->users->idByCredentials($email, $apiKey);
    }

    public function provideToken(int $userId): Token
    {
        $token = $this->tokens->findActiveByUserId($userId);

        if (! $token || $token->isExpired()) {
            $token = $this->generateAndSaveToken($userId);
        }

        return $token;
    }

    public function tokenIsActive(string $value): bool
    {
        $token = $this->tokens->findByValue($value);

        return $token && ! $token->isExpired();
    }

    /**
     * @throws RandomException
     */
    private function generateAndSaveToken(int $userId): Token
    {
        $token = new Token(
            $this->generator->generate(),
            $userId,
            new DateTimeImmutable('+3 days'),
        );

        $this->tokens->save($token);

        return $token;
    }
}
