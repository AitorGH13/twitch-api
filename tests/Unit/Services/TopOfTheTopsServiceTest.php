<?php

namespace Unit\Services;

use App\Exceptions\NoGamesFoundException;
use App\Exceptions\NoVideosFoundException;
use App\Exceptions\UnauthorizedException;
use App\Services\AuthService;
use App\Services\TopOfTheTopsService;
use App\Interfaces\TwitchClientInterface;
use App\Interfaces\TopOfTheTopsRepositoryInterface;
use Unit\BaseUnitTestCase;

class TopOfTheTopsServiceTest extends BaseUnitTestCase
{
    /** @test */
    public function invalidTokenThrowsUnauthorizedException()
    {
        $repoMock = $this->mock(TopOfTheTopsRepositoryInterface::class);
        $authMock = $this->mock(AuthService::class);
        $clientMock = $this->mock(TwitchClientInterface::class);

        $invalidAccessToken = 'badToken';

        $authMock->shouldReceive('validateAccessToken')
            ->once()
            ->with($invalidAccessToken)
            ->andReturnFalse();

        $repoMock->shouldNotReceive('getCacheMeta');

        $topOfTheTopsService = new TopOfTheTopsService(
            $repoMock,
            $authMock,
            $clientMock
        );

        $this->expectException(UnauthorizedException::class);
        $topOfTheTopsService->getTopOfTheTops([$invalidAccessToken, null]);
    }

    /** @test */
    public function whenClientReturnsEmptyGamesThrowsNoGamesFoundException()
    {
        $repoMock = $this->mock(TopOfTheTopsRepositoryInterface::class);
        $authMock = $this->mock(AuthService::class);
        $clientMock = $this->mock(TwitchClientInterface::class);

        $validAccessToken = 'validToken';
        $topGamesLimit = 3;
        $requestParams = [$validAccessToken, null]; // [accessToken, sinceTimestamp]

        $authMock->shouldReceive('validateAccessToken')
            ->once()
            ->with($validAccessToken)
            ->andReturnTrue();

        $repoMock->shouldReceive('getCacheMeta')
            ->once()
            ->andReturnNull();

        $repoMock->shouldReceive('clearCache')
            ->once();

        $clientMock->shouldReceive('getTopGames')
            ->once()
            ->with($topGamesLimit)
            ->andReturn([]);

        $topOfTheTopsService = new TopOfTheTopsService(
            $repoMock,
            $authMock,
            $clientMock
        );

        $this->expectException(NoGamesFoundException::class);
        $topOfTheTopsService->getTopOfTheTops($requestParams);
    }

    /** @test */
    public function whenFirstGameHasNoVideosThrowsNoVideosFoundException()
    {
        $repoMock = $this->mock(TopOfTheTopsRepositoryInterface::class);
        $authMock = $this->mock(AuthService::class);
        $clientMock = $this->mock(TwitchClientInterface::class);

        $validAccessToken = 'validToken';
        $topGamesLimit = 3;
        $topVideosLimit = 40;
        $requestParams = [$validAccessToken, null]; // [accessToken, sinceTimestamp]

        $mockGameData = [
            ['id' => '1', 'name' => 'Game1'],
        ];

        $firstGameId = '1';

        $authMock->shouldReceive('validateAccessToken')
            ->once()
            ->with($validAccessToken)
            ->andReturnTrue();

        $repoMock->shouldReceive('getCacheMeta')
            ->once()
            ->andReturnNull();

        $repoMock->shouldReceive('clearCache')
            ->once();

        $clientMock->shouldReceive('getTopGames')
            ->once()
            ->with($topGamesLimit)
            ->andReturn($mockGameData);

        $clientMock->shouldReceive('getTopVideos')
            ->once()
            ->with($firstGameId, $topVideosLimit)
            ->andReturn([]);

        $topOfTheTopsService = new TopOfTheTopsService(
            $repoMock,
            $authMock,
            $clientMock
        );

        $this->expectException(NoVideosFoundException::class);
        $topOfTheTopsService->getTopOfTheTops($requestParams);
    }
}
