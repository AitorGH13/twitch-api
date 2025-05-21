<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthMiddleware
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $header = $request->header('Authorization', '');
        if (! str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'error' => 'Unauthorized. Twitch access token is invalid or has expired.'
            ], 401);
        }

        $token = substr($header, 7);
        if (! $this->authService->validateAccessToken($token)) {
            return response()->json([
                'error' => 'Unauthorized. Twitch access token is invalid or has expired.'
            ], 401);
        }

        $request->attributes->set('token', $token);

        return $next($request);
    }
}
