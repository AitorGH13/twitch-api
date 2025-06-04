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
        $mockAuthService = $this->mock(AuthService::class);
        $mockTwitchClient = $this->mock(TwitchClientInterface::class);

        $invalidAccessToken = 'badToken';
        $requestedStreamLimit = 10;

        $mockAuthService->shouldReceive('validateAccessToken')
            ->once()
            ->with($invalidAccessToken)
            ->andReturnFalse();

        $mockTwitchClient->shouldNotReceive('getLiveStreams');

        $streamsService = new StreamsService($mockAuthService, $mockTwitchClient);

        $this->expectException(UnauthorizedException::class);
        $streamsService->getLiveStreams($invalidAccessToken, $requestedStreamLimit);
    }

    /** @test */
    public function validTokenReturnsMappedStreams()
    {
        $mockAuthService = $this->mock(AuthService::class);
        $mockTwitchClient = $this->mock(TwitchClientInterface::class);

        $validAccessToken = 'goodToken';
        $requestedStreamLimit = 3;

        $mockStreamData = [
            ['title' => 'Title1', 'user_name' => 'test1', 'viewer_count' => 50],
            ['title' => 'Title2', 'user_name' => 'test2', 'viewer_count' => 60],
        ];

        $mappedStreams = [
            ['title' => 'Title1', 'user_name' => 'test1'],
            ['title' => 'Title2', 'user_name' => 'test2'],
        ];

        $mockAuthService->shouldReceive('validateAccessToken')
            ->once()
            ->with($validAccessToken)
            ->andReturnTrue();

        $mockTwitchClient->shouldReceive('getLiveStreams')
            ->once()
            ->with($requestedStreamLimit)
            ->andReturn($mockStreamData);

        $streamsService = new StreamsService($mockAuthService, $mockTwitchClient);

        $returnedStreams = $streamsService->getLiveStreams($validAccessToken, $requestedStreamLimit);

        $this->assertSame($mappedStreams, $returnedStreams);
    }
}
