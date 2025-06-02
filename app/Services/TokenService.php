<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use App\Exceptions\InvalidApiKeyException;
use App\Manager\TokenManager;

class TokenService
{
    public function __construct(private readonly TokenManager $manager)
    {
    }

    /**
     * Devuelve un access-token válido para el par (email, apiKey).
     */
    public function createToken(string $email, string $apiKey): JsonResponse
    {
        $userId = $this->manager->checkUser($email, $apiKey);

        if (! $userId) {
            throw new InvalidApiKeyException();
        }

        $token = $this->manager->provideToken($userId);

        return new JsonResponse([
            'token'      => $token->value,
        ], 200);
    }

    /**
     * Valida un access-token.
     */
    public function validateAccessToken(string $token): bool
    {
        return $this->manager->tokenIsActive($token);
    }
}
