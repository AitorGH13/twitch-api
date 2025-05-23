<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Validators\TokenRequestValidator;
use App\Services\TokenService;
use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailAddressException;
use App\Exceptions\EmptyApiKeyException;
use App\Exceptions\InvalidApiKeyException;
use Random\RandomException;

class TokenController extends BaseController
{
    public function __construct(
        private readonly TokenRequestValidator $validator,
        private readonly TokenService $service
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            ['email' => $email, 'api_key' => $apiKey] = $this->validator->validate($request);
            return $this->service->createToken($email, $apiKey);
        } catch (
            EmptyEmailException |
            InvalidEmailAddressException |
            EmptyApiKeyException $e
        ) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (InvalidApiKeyException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
