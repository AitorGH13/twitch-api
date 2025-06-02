<?php

namespace Unit\Services;

use App\Exceptions\UnauthorizedException;
use App\Services\AuthService;
use App\Services\StreamsService;
use App\Interfaces\TwitchClientInterface;
use Unit\BaseUnitTestCase;

class StreamsServiceTest extends BaseUnitTestCase
{
    /** @test */
    public function invalidTokenThrowsUnauthorizedException()
    {
        $auth = $this->mock(AuthService::class);
        $auth->shouldReceive('validateAccessToken')
            ->once()->with('badToken')->andReturnFalse();

        $client = $this->mock(TwitchClientInterface::class);
        $client->shouldNotReceive('getLiveStreams');

        $service = new StreamsService($auth, $client);

        $this->expectException(UnauthorizedException::class);
        $service->getLiveStreams('badToken', 10);
    }

    /** @test */
    public function validTokenReturnsMappedStreams()
    {
        $auth = $this->mock(AuthService::class);
        $auth->shouldReceive('validateAccessToken')
            ->once()->with('goodToken')->andReturnTrue();

        $client = $this->mock(TwitchClientInterface::class);
        $client->shouldReceive('getLiveStreams')
            ->once()->with(3)
            ->andReturn([
                ['title' => 'Title1', 'user_name' => 'test1', 'viewer_count' => 50],
                ['title' => 'Title2', 'user_name' => 'test2',   'viewer_count' => 60],
            ]);

        $service = new StreamsService($auth, $client);

        $result = $service->getLiveStreams('goodToken', 3);

        $this->assertSame([
            ['title' => 'Title1', 'user_name' => 'test1'],
            ['title' => 'Title2', 'user_name' => 'test2'],
        ], $result);
    }
}
