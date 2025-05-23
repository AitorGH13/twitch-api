<?php

namespace App\Manager;

use Illuminate\Http\JsonResponse;
use App\Repository\DatabaseRepository;

class TokenManager
{
    private DatabaseRepository $repo;

    public function __construct(DatabaseRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Comprueba credenciales y devuelve el user_id o null.
     */
    public function checkUser(string $email, string $apiKey): ?int
    {
        return $this->repo->getUserIdByCredentials($email, $apiKey);
    }

    /**
     * Devuelve JsonResponse con token y expires_at si existe sesión.
     */
    public function getToken(int $userId): JsonResponse
    {
        $session = $this->repo->getSession($userId);

        if ($session) {
            return new JsonResponse([
                'token'      => $session->token,
                'expires_at' => $session->expires_at,
            ], 200);
        }

        return new JsonResponse(['token' => null, 'expires_at' => null], 200);
    }

    /**
     * Genera un nuevo token y fecha de expiración (3 días).
     */
    public function generateToken(): JsonResponse
    {
        $token     = bin2hex(random_bytes(16));
        $expiresAt = date('Y-m-d H:i:s', time() + 3 * 24 * 3600);

        return new JsonResponse([
            'token'      => $token,
            'expires_at' => $expiresAt,
        ], 200);
    }

    /**
     * Registra o actualiza en BD el token y su expiración.
     */
    public function updateToken(string $token, string $expiresAt, int $userId): void
    {
        $session = $this->repo->getSession($userId);

        if ($session) {
            $this->repo->updateSession($userId, $token, $expiresAt);
        }

        $this->repo->registerSession($userId, $token, $expiresAt);
    }

    /**
     * Comprueba si un token dado sigue activo.
     */
    public function tokenIsActive(string $token): bool
    {
        $session = $this->repo->getSessionByToken($token);

        if (! $session) {
            return false;
        }

        return strtotime($session->expires_at) >= time();
    }
}
