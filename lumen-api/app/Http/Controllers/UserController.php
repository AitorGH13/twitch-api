<?php // app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Validators\UserRequestValidator;
use App\Services\UserService;
use App\Exceptions\EmptyIdException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UnauthorizedException;

class UserController extends BaseController
{
    public function __construct(
        private UserRequestValidator $validator,
        private UserService          $service
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            [$id, $token] = $this->validator->validate($request);
            if ($token === '') {
                return response()->json([
                    'error' => 'Unauthorized. Twitch access token is invalid or has expired.'
                ], 401);
            }
            $user = $this->service->get([$id, $token]);
            return response()->json($user, 200);
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (EmptyIdException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (UserNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
