<?php

namespace App\Interfaces;

use App\Domain\Token;

interface TokenRepositoryInterface
{
    public function findActiveByUserId(int $userId): ?Token;

    public function findByValue(string $value): ?Token;

    public function save(Token $token): void;
}
