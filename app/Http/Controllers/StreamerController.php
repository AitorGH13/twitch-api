<?php

namespace App\Http\Controllers;

use Exception;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Middleware\AuthMiddleware;
use App\Validators\StreamerValidator;
use App\Services\StreamerService;
use App\Exceptions\EmptyIdException;
use App\Exceptions\UserNotFoundException;

class StreamerController extends BaseController
{
    public function __construct(
        private readonly StreamerValidator $validator,
        private readonly StreamerService $service
    ) {
        $this->middleware(AuthMiddleware::class);
    }

    /**
     * @throws Exception
     */
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
