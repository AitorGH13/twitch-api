<?php

namespace App\Domain;

use DateTimeImmutable;

final class Token
{
    public function __construct(
        public readonly string $value,
        public readonly int $userId,
        public readonly DateTimeImmutable $expiresAt,
    ) {
    }

    public function isExpired(): bool
    {
        return $this->expiresAt <= new DateTimeImmutable();
    }
}
