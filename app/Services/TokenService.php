<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use App\Exceptions\InvalidApiKeyException;
use App\Manager\TokenManager;

class TokenService
{
    private TokenManager $manager;

    public function __construct(TokenManager $manager)
    {
        $this->manager = $manager;
    }

    public function createToken(string $email, string $apiKey): JsonResponse
    {
        $userId = $this->manager->checkUser($email, $apiKey);

        if (! $userId) {
            throw new InvalidApiKeyException();
        }

        $response = $this->manager->getToken($userId);
        $data     = json_decode($response->getContent(), true);
        $token    = $data['token'];
        $expires  = $data['expires_at'];

        if (! $token || strtotime($expires) < time()) {
            $newResp = $this->manager->generateToken();
            $newData = json_decode($newResp->getContent(), true);

            $token   = $newData['token'];
            $expires = $newData['expires_at'];

            $this->manager->updateToken($token, $expires, $userId);
        }

        // Devolver token:
        return new JsonResponse([
            'token'      => $token,
        ], 200);
    }

    /**
     * Valida un access token.
     */
    public function validateAccessToken(string $token): bool
    {
        return $this->manager->tokenIsActive($token);
    }
}
