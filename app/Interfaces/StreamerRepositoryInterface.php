<?php

namespace App\Interfaces;

interface StreamerRepositoryInterface
{
    public function findById(string $userId): ?array;

    public function insert(array $user): void;
}
