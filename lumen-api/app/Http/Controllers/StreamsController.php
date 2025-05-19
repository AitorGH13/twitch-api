<?php // app/Http/Controllers/StreamsController.php
namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Validators\StreamsRequestValidator;
use App\Services\StreamsService;
use App\Exceptions\UnauthorizedException;

class StreamsController extends BaseController
{
    public function __construct(
        private StreamsRequestValidator $validator,
        private StreamsService          $service
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $token    = $this->validator->validate($request);
            $streams  = $this->service->getLiveStreams($token);
            return response()->json($streams, 200);
        } catch (UnauthorizedException $e) {
            return response()->json(
                ['error' => 'Unauthorized. Twitch access token is invalid or has expired.'],
                401
            );
        }
    }
}
