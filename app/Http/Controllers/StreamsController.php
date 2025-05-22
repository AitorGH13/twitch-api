<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Middleware\AuthMiddleware;
use App\Validators\StreamsRequestValidator;
use App\Services\StreamsService;

class StreamsController extends BaseController
{
    public function __construct(
        private readonly StreamsRequestValidator $validator,
        private readonly StreamsService $service
    ) {
        $this->middleware(AuthMiddleware::class);
    }

    public function index(Request $request): JsonResponse
    {
        $token   = $this->validator->validate($request);
        $streams = $this->service->getLiveStreams($token, 3);
        return response()->json(
            $streams,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
