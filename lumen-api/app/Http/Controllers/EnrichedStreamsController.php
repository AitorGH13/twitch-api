<?php // app/Http/Controllers/EnrichedStreamsController.php
namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Validators\EnrichedStreamsRequestValidator;
use App\Services\EnrichedStreamsService;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\InvalidLimitException;

class EnrichedStreamsController extends BaseController
{
    public function __construct(
        private EnrichedStreamsRequestValidator $validator,
        private EnrichedStreamsService          $service
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            [$limit, $token] = $this->validator->validate($request);

            if ($token === '') {
                // 401 antes de business
                throw new UnauthorizedException();
            }

            $data = $this->service->getTopEnrichedStreams($limit, $token);
            //return response()->json($data, 200);
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
