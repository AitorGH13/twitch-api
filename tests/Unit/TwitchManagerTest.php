<?php

namespace Tests\Unit;

use Tests\BaseUnitTestCase;
use App\Manager\TwitchManager;
use App\Services\TwitchAuthService;

class TwitchManagerTest extends BaseUnitTestCase
{
    /** @test */
    public function getTopGamesReturnsStubbedArrayInTestingEnv()
    {
        $auth = $this->mock(TwitchAuthService::class);

        putenv('APP_ENV=testing');

        $manager = new TwitchManager($auth);

        $games = $manager->getTopGames(3);

        $this->assertCount(3, $games);
        $this->assertEquals('Game2', $games[1]['name']);
    }
}
