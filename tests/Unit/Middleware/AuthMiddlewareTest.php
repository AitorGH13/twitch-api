<?php

namespace Unit\Middleware;

use App\Http\Middleware\AuthMiddleware;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Unit\BaseUnitTestCase;

class AuthMiddlewareTest extends BaseUnitTestCase
{
    /**
     * Creates a test HTTP request with optional headers
     *
     * @param array $headers Key-value pairs of HTTP headers to add to the request
     * @return Request The constructed request object
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    private function createTestRequest(array $headers = []): Request
    {
        $testRequest = Request::create('/dummy-endpoint', 'GET');

        foreach ($headers as $headerName => $headerValue) {
            $testRequest->headers->set($headerName, $headerValue);
        }

        return $testRequest;
    }

    /**
     * @test
     */
    public function authorizationHeaderIsMissingReturns401()
    {
        $mockAuthService = $this->mock(AuthService::class);

        $mockAuthService->shouldNotReceive('validateAccessToken');

        $authMiddleware = new AuthMiddleware($mockAuthService);

        $expectedStatusCode = 401;
        $expectedErrorMessage = 'Unauthorized. Twitch access token is invalid or has expired.';

        $response = $authMiddleware->handle(
            $this->createTestRequest(), // Empty request with no headers
            function () {
                $this->fail('Request should not reach the next middleware when Authorization header is missing');
            }
        );

        $this->assertSame($expectedStatusCode, $response->getStatusCode());
        $this->assertSame(
            ['error' => $expectedErrorMessage],
            $response->getData(true)
        );
    }

    /**
     * @test
     */
    public function tokenIsInvalidReturns401()
    {
        $mockAuthService = $this->mock(AuthService::class);
        $invalidAccessToken = 'badToken';
        $authHeaderValue = "Bearer {$invalidAccessToken}";
        $expectedStatusCode = 401;

        $mockAuthService->shouldReceive('validateAccessToken')
            ->once()
            ->with($invalidAccessToken)
            ->andReturnFalse();

        $authMiddleware = new AuthMiddleware($mockAuthService);

        $response = $authMiddleware->handle(
            $this->createTestRequest(['Authorization' => $authHeaderValue]),
            function () {
                $this->fail('Request should not reach the next middleware with invalid token');
            }
        );

        $this->assertSame($expectedStatusCode, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function whenTokenIsValidCallsNextAndSetsTokenAttribute()
    {
        $mockAuthService = $this->mock(AuthService::class);
        $validAccessToken = 'goodToken';
        $authHeaderValue = "Bearer {$validAccessToken}";
        $expectedStatusCode = 200;
        $tokenAttributeName = 'token';

        $mockAuthService->shouldReceive('validateAccessToken')
            ->once()
            ->with($validAccessToken)
            ->andReturnTrue();

        $authMiddleware = new AuthMiddleware($mockAuthService);

        $capturedRequest = null;

        $response = $authMiddleware->handle(
            $this->createTestRequest(['Authorization' => $authHeaderValue]),
            function ($request) use (&$capturedRequest) {
                $capturedRequest = $request;
                return new Response(200);
            }
        );

        $this->assertSame($expectedStatusCode, $response->getStatusCode());

        $this->assertSame(
            $validAccessToken,
            $capturedRequest->attributes->get($tokenAttributeName),
            'The access token should be available in the request attributes'
        );
    }
}
