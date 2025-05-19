<?php // app/Http/Controllers/TopOfTheTopsController.php
namespace App\Http\Controllers;

use App\Exceptions\InvalidSinceException;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Validators\TopOfTheTopsRequestValidator;
use App\Services\TopOfTheTopsService;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\NoGamesFoundException;
use App\Exceptions\NoVideosFoundException;

class TopOfTheTopsController extends BaseController
{
    public function __construct(
        private TopOfTheTopsRequestValidator $validator,
        private TopOfTheTopsService          $service
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $input    = $this->validator->validate($request);
            $response = $this->service->get($input);
            //return response()->json($response, 200);
            return response()->json($response, 200, [], JSON_UNESCAPED_UNICODE);
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (NoGamesFoundException | NoVideosFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (InvalidSinceException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
