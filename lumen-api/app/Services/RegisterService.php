<?php

namespace App\Services;

use App\Repository\DatabaseRepository;
use Illuminate\Http\JsonResponse;

class RegisterService
{
    private $repo;

    public function __construct(DatabaseRepository $repo)
    {
        $this->repo = $repo;
    }

    public function registerUser(string $email): JsonResponse
    {
        $apiKey = bin2hex(random_bytes(16));
        $user   = $this->repo->getUserByEmail($email);

        if ($user) {
            $this->repo->updateApiKey($email, $apiKey);
        }

        $this->repo->registerEmailAndApiKey($email, $apiKey);

        return new JsonResponse(['api_key' => $apiKey], 200);
    }
}
