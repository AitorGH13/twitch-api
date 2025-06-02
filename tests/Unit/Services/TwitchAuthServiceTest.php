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
        $cache = $this->mock(CacheRepository::class);
        $http  = $this->mock(HttpClient::class);
        $resp  = $this->mock(Response::class);

        $resp->shouldReceive('ok')->andReturnFalse();

        $cache->shouldReceive('remember')
            ->once()
            ->withArgs(function ($key, $ttl, $callback) use ($resp) {
                $this->assertSame('twitch_app_token', $key);

                $expectedTtl = (int) env('TWITCH_TOKEN_TTL', 3600) - 60;
                $this->assertSame($expectedTtl, $ttl);

                $this->assertIsCallable($callback);

                return $callback();
            });

        $http->shouldReceive('asForm')->andReturnSelf();
        $http->shouldReceive('post')->andReturn($resp);

        $service = new TwitchAuthService($cache, $http);

        $this->expectException(RuntimeException::class);
        $service->getAppAccessToken();
    }
}
