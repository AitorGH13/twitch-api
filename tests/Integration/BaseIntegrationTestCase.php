<?php

namespace Integration;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\TestCase;
use App\Interfaces\TwitchClientInterface;
use Fakes\FakeTwitchClient;

abstract class BaseIntegrationTestCase extends TestCase
{
    use DatabaseMigrations;

    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(
            TwitchClientInterface::class,
            new FakeTwitchClient()
        );
    }
}
