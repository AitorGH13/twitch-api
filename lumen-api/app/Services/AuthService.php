<?php

// app/Services/AuthService.php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use App\Services\RegisterService;
use App\Services\TokenService;
use App\Exceptions\InvalidApiKeyException;

class AuthService
{
    private $registerService;
    private $tokenService;

    public function __construct(
        RegisterService $registerService,
        TokenService $tokenService
    ) {
        $this->registerService = $registerService;
        $this->tokenService    = $tokenService;
    }

    /**
     * Devuelve el api_key como string (no JsonResponse),
     * para satisfacer a los tests que llaman a app(AuthService::class)->registerEmail()
     */
    public function registerEmail(string $email): string
    {
        $response = $this->registerService->registerUser($email);
        // getData(true) devuelve el body como array
        $data = $response->getData(true);
        return $data['api_key'];
    }

    /**
     * Devuelve el token como string,
     * para que app(AuthService::class)->createAccessToken() funcione.
     * Lanza InvalidApiKeyException si no coincide.
     */
    public function createAccessToken(string $email, string $apiKey): string
    {
        $response = $this->tokenService->createToken($email, $apiKey);
        $data     = $response->getData(true);
        return $data['token'];
    }

    /**
     * Proxy a TokenService::validateAccessToken()
     */
    public function validateAccessToken(string $token): bool
    {
        return $this->tokenService->validateAccessToken($token);
    }
}
