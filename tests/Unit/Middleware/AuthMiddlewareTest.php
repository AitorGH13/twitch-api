<?php

namespace Unit\Middleware;

use App\Http\Middleware\AuthMiddleware;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Unit\BaseUnitTestCase;

class AuthMiddlewareTest extends BaseUnitTestCase
{
    /** @SuppressWarnings(PHPMD.StaticAccess) */
    private function makeRequest(array $headers = []): Request
    {
        $request = Request::create('/dummy', 'GET');

        foreach ($headers as $key => $value) {
            $request->headers->set($key, $value);
        }

        return $request;
    }

    /** @test */
    public function authorizationHeaderIsMissingReturns401()
    {
        $authService = $this->mock(AuthService::class);
        $authService->shouldNotReceive('validateAccessToken');

        $middleware = new AuthMiddleware($authService);

        $response = $middleware->handle(
            $this->makeRequest(),
            fn () => $this->fail('No debería llegar al siguiente middleware')
        );

        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame(
            ['error' => 'Unauthorized. Twitch access token is invalid or has expired.'],
            $response->getData(true)
        );
    }

    /** @test */
    public function tokenIsInvalidReturns401()
    {
        $authService = $this->mock(AuthService::class);
        $authService->shouldReceive('validateAccessToken')
            ->once()
            ->with('badToken')
            ->andReturnFalse();

        $middleware = new AuthMiddleware($authService);

        $response = $middleware->handle(
            $this->makeRequest(['Authorization' => 'Bearer badToken']),
            fn () => $this->fail('No debería llegar al siguiente middleware')
        );

        $this->assertSame(401, $response->getStatusCode());
    }

    /** @test */
    public function whenTokenIsValidCallsNextAndSetsTokenAttribute()
    {
        $authService = $this->mock(AuthService::class);
        $authService->shouldReceive('validateAccessToken')
            ->once()
            ->with('goodToken')
            ->andReturnTrue();

        $middleware = new AuthMiddleware($authService);

        $capturedRequest = null;

        $response = $middleware->handle(
            $this->makeRequest(['Authorization' => 'Bearer goodToken']),
            function ($request) use (&$capturedRequest) {
                $capturedRequest = $request;
                return new Response(200);
            }
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('goodToken', $capturedRequest->attributes->get('token'));
    }
}
