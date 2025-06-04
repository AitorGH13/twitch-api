<?php

namespace App\Interfaces;

use stdClass;

interface UserRepositoryInterface
{
    public function getByEmail(string $email): ?stdClass;

    public function register(string $email, string $apiKey): void;

    public function updateApiKey(string $email, string $apiKey): void;

    public function idByCredentials(string $email, string $apiKey): ?int;
}
