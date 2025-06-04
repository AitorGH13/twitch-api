<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Random\RandomException;

class RegisterService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws RandomException
     */
    public function registerUser(string $email): JsonResponse
    {
        $apiKey = bin2hex(random_bytes(16));
        $user   = $this->userRepository->getByEmail($email);

        if ($user) {
            $this->userRepository->updateApiKey($email, $apiKey);
        }

        $this->userRepository->register($email, $apiKey);

        return new JsonResponse(['api_key' => $apiKey], 200);
    }
}
