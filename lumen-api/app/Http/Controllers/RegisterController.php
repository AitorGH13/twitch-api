<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Validators\RegisterRequestValidator;
use App\Services\RegisterService;
use App\Exceptions\EmptyEmailException;
use App\Exceptions\InvalidEmailAddressException;
use Random\RandomException;

class RegisterController extends BaseController
{
    public function __construct(
        private readonly RegisterRequestValidator $validator,
        private readonly RegisterService $service
    ) {
    }

    /**
     * @throws RandomException
     */
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $email = $this->validator->validate($request);
            return $this->service->registerUser($email);
        } catch (EmptyEmailException | InvalidEmailAddressException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
