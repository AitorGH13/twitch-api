<?php

namespace Unit\Clients;

use Unit\BaseUnitTestCase;
use Fakes\FakeTwitchClient;
use App\Interfaces\TwitchClientInterface;

class TwitchClientTest extends BaseUnitTestCase
{
    /** @test */
    public function getTopGamesReturnsStubbedArray()
    {
        /** @var TwitchClientInterface $client */
        $client = new FakeTwitchClient();

        $games = $client->getTopGames(3);

        $this->assertCount(3, $games);
        $this->assertEquals('Game2', $games[1]['name']);
    }
}
