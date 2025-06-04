<?php

namespace App\Interfaces;

use stdClass;

interface TopOfTheTopsRepositoryInterface
{
    public function getCacheMeta(): ?stdClass;

    public function clearCache(): void;

    public function insert(array $row, string $expiresAt): void;

    public function all(): array;
}
