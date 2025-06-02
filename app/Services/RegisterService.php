<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Random\RandomException;

class RegisterService
{
    private UserRepositoryInterface $repo;

    public function __construct(UserRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @throws RandomException
     */
    public function registerUser(string $email): JsonResponse
    {
        $apiKey = bin2hex(random_bytes(16));
        $user   = $this->repo->getByEmail($email);

        if ($user) {
            $this->repo->updateApiKey($email, $apiKey);
        }

        $this->repo->register($email, $apiKey);

        return new JsonResponse(['api_key' => $apiKey], 200);
    }
}
