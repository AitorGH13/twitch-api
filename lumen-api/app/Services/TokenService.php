<?php

namespace App\Services;

use App\Repository\DatabaseRepository;
use Illuminate\Http\JsonResponse;
use App\Exceptions\InvalidApiKeyException;

class TokenService
{
    private $repo;

    public function __construct(DatabaseRepository $repo)
    {
        $this->repo = $repo;
    }

    public function createToken(string $email, string $apiKey): JsonResponse
    {
        $userId = $this->repo->getUserIdByCredentials($email, $apiKey);
        if (! $userId) {
            throw new InvalidApiKeyException();
        }

        $now       = time();
        $expiresAt = $now + 3 * 24 * 3600;
        $session   = $this->repo->getSession($userId);

        if ($session && strtotime($session->expires_at) >= $now) {
            return new JsonResponse(['token' => $session->token], 200);
        }

        $token = bin2hex(random_bytes(16));

        if ($session) {
            $this->repo->updateSession($userId, $token, $expiresAt);
            return new JsonResponse(['token' => $token], 200);
        }

        $this->repo->registerSession($userId, $token, $expiresAt);
        return new JsonResponse(['token' => $token], 200);
    }

    /**
     * Valida un access token.
     */
    public function validateAccessToken(string $token): bool
    {
        $session = $this->repo->getSessionByToken($token);
        if (! $session) {
            return false;
        }
        return strtotime($session->expires_at) >= time();
    }
}
