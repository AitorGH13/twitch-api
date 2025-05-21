<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Middleware\AuthMiddleware;
use App\Validators\UserRequestValidator;
use App\Services\UserService;
use App\Exceptions\EmptyIdException;
use App\Exceptions\UserNotFoundException;

class UserController extends BaseController
{
    public function __construct(
        private UserRequestValidator $validator,
        private UserService $service
    ) {
        $this->middleware(AuthMiddleware::class);
    }

    public function profile(Request $request): JsonResponse
    {
        try {
            [$userId, $token] = $this->validator->validate($request);
            $user = $this->service->getUserProfile([$userId, $token]);
            return response()->json(
                $user,
                200,
                [],
                JSON_UNESCAPED_UNICODE
            );
        } catch (EmptyIdException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (UserNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
