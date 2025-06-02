<?php

namespace Unit\Services;

use App\Exceptions\NoGamesFoundException;
use App\Exceptions\NoVideosFoundException;
use App\Exceptions\UnauthorizedException;
use App\Services\AuthService;
use App\Services\TopOfTheTopsService;
use App\Interfaces\TwitchClientInterface;
use App\Repository\TopOfTheTopsRepository;
use Unit\BaseUnitTestCase;

class TopOfTheTopsServiceTest extends BaseUnitTestCase
{
    /** @test */
    public function invalidTokenThrowsUnauthorized()
    {
        $repo   = $this->mock(TopOfTheTopsRepository::class);
        $auth   = $this->mock(AuthService::class);
        $client = $this->mock(TwitchClientInterface::class);

        $auth->shouldReceive('validateAccessToken')->once()->with('badToken')->andReturnFalse();
        $repo->shouldNotReceive('getCacheMeta');

        $service = new TopOfTheTopsService($repo, $auth, $client);

        $this->expectException(UnauthorizedException::class);
        $service->getTopOfTheTops(['badToken', null]);
    }

    /** @test */
    public function whenClientReturnsEmptyGamesThrowsNoGamesFound()
    {
        $repo   = $this->mock(TopOfTheTopsRepository::class);
        $auth   = $this->mock(AuthService::class);
        $client = $this->mock(TwitchClientInterface::class);

        $auth->shouldReceive('validateAccessToken')->once()->with('validToken')->andReturnTrue();
        $repo->shouldReceive('getCacheMeta')->once()->andReturnNull();
        $repo->shouldReceive('clearCache')->once();

        $client->shouldReceive('getTopGames')->once()->with(3)->andReturn([]);

        $service = new TopOfTheTopsService($repo, $auth, $client);

        $this->expectException(NoGamesFoundException::class);
        $service->getTopOfTheTops(['validToken', null]);
    }

    /** @test */
    public function whenFirstGameHasNoVideosThrowsNoVideosFound()
    {
        $repo   = $this->mock(TopOfTheTopsRepository::class);
        $auth   = $this->mock(AuthService::class);
        $client = $this->mock(TwitchClientInterface::class);

        $auth->shouldReceive('validateAccessToken')->once()->with('validToken')->andReturnTrue();
        $repo->shouldReceive('getCacheMeta')->once()->andReturnNull();
        $repo->shouldReceive('clearCache')->once();

        $client->shouldReceive('getTopGames')->once()->with(3)->andReturn([
            ['id' => '1', 'name' => 'Game1'],
        ]);

        $client->shouldReceive('getTopVideos')
            ->once()->with('1', 40)->andReturn([]);

        $service = new TopOfTheTopsService($repo, $auth, $client);

        $this->expectException(NoVideosFoundException::class);
        $service->getTopOfTheTops(['validToken', null]);
    }
}
