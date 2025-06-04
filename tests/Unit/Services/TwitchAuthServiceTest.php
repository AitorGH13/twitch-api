<?php

namespace Unit\Services;

use App\Services\TwitchAuthService;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\Response;
use RuntimeException;
use Unit\BaseUnitTestCase;

class TwitchAuthServiceTest extends BaseUnitTestCase
{
    /** @test */
    public function whenHttpClientFailsThrowsException()
    {
        $mockCacheRepository = $this->mock(CacheRepository::class);
        $mockHttpClient = $this->mock(HttpClient::class);
        $mockHttpResponse = $this->mock(Response::class);

        $mockHttpResponse->shouldReceive('ok')->andReturnFalse();

        $expectedCacheKey = 'twitch_app_token';
        $defaultTokenTtl = 3600;
        $bufferTime = 60;

        $mockCacheRepository->shouldReceive('remember')
            ->once()
            ->withArgs(
                function (
                    $cacheKey,
                    $cacheExpirationTime,
                    $tokenGenerationCallback
                ) use (
                    $mockHttpResponse,
                    $expectedCacheKey,
                    $defaultTokenTtl,
                    $bufferTime
                ) {
                    $this->assertSame($expectedCacheKey, $cacheKey);

                    $expectedCacheExpirationTime = (int) env('TWITCH_TOKEN_TTL', $defaultTokenTtl)
                        - $bufferTime;
                    $this->assertSame($expectedCacheExpirationTime, $cacheExpirationTime);

                    $this->assertIsCallable($tokenGenerationCallback);

                    return $tokenGenerationCallback();
                }
            );

        $mockHttpClient->shouldReceive('asForm')->andReturnSelf();
        $mockHttpClient->shouldReceive('post')->andReturn($mockHttpResponse);

        $twitchAuthService = new TwitchAuthService($mockCacheRepository, $mockHttpClient);

        $this->expectException(RuntimeException::class);
        $twitchAuthService->getAppAccessToken();
    }
}
