<?php

// app/Http/Controllers/EnrichedStreamsController.php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Middleware\AuthMiddleware;
use App\Validators\EnrichedStreamsRequestValidator;
use App\Services\EnrichedStreamsService;
use App\Exceptions\InvalidLimitException;
use App\Exceptions\UnauthorizedException;

class EnrichedStreamsController extends BaseController
{
    public function __construct(
        private EnrichedStreamsRequestValidator $validator,
        private EnrichedStreamsService $service
    ) {
        $this->middleware(AuthMiddleware::class);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            [$limit, $token] = $this->validator->validate($request);
            $data = $this->service->getTopEnrichedStreams($limit, $token);
            return response()->json(
                $data,
                200,
                [],
                JSON_UNESCAPED_UNICODE
            );
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (InvalidLimitException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
