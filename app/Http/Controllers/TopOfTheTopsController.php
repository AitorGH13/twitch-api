<?php

namespace App\Http\Controllers;

use Exception;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Middleware\AuthMiddleware;
use App\Validators\TopOfTheTopsValidator;
use App\Services\TopOfTheTopsService;
use App\Exceptions\InvalidSinceException;
use App\Exceptions\NoGamesFoundException;
use App\Exceptions\NoVideosFoundException;

class TopOfTheTopsController extends BaseController
{
    public function __construct(
        private readonly TopOfTheTopsValidator $validator,
        private readonly TopOfTheTopsService $service
    ) {
        $this->middleware(AuthMiddleware::class);
    }

    /**
     * @throws Exception
     */
    public function list(Request $request): JsonResponse
    {
        try {
            [$token, $since] = $this->validator->validate($request);
            $data = $this->service->getTopOfTheTops([$token, $since]);
            return response()->json(
                $data,
                200,
                [],
                JSON_UNESCAPED_UNICODE
            );
        } catch (InvalidSinceException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (NoGamesFoundException | NoVideosFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
