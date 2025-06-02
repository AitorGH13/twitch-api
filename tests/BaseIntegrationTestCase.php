<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Mockery\Exception\RuntimeException;
use Mockery\MockInterface;
use Mockery\Container;

abstract class BaseIntegrationTestCase extends TestCase
{
    use DatabaseMigrations;

    /** @var Container|null Contenedor de Mockery para este test */
    private ?Container $mockeryContainer = null;

    /** @var MockInterface|null Mock de TwitchManager */
    private ?MockInterface $twitchManagerMock = null;

    public function createApplication()
    {
        putenv('APP_ENV=testing');          // Activa configuración de test
        return require __DIR__ . '/../bootstrap/app.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->swapDependencies();
    }

    protected function tearDown(): void
    {
        if ($this->mockeryContainer instanceof Container) {
            $this->mockeryContainer->mockery_close();
        }

        parent::tearDown();
    }

    protected function swapDependencies(): void
    {
        $this->swapTwitchManager();
    }

    /**
     * @throws \ReflectionException
     * @throws RuntimeException
     */
    private function swapTwitchManager(): void
    {
        // Creamos un contenedor de Mockery específico para este test
        $this->mockeryContainer = new Container();

        // Generamos el mock “alias” dentro de ese contenedor
        $this->twitchManagerMock = $this->mockeryContainer->mock('alias:App\\Manager\\TwitchManager');

        $this->twitchManagerMock
            ->shouldReceive('getLiveStreams')
            ->andReturnUsing(
                fn (int $limit) => [
                    ['title' => 'Title of Stream 1', 'user_name' => 'User1'],
                    ['title' => 'Title of Stream 2', 'user_name' => 'User2'],
                    ['title' => 'Title of Stream 3', 'user_name' => 'User3'],
                ]
            );

        $this->twitchManagerMock
            ->shouldReceive('getStreams')
            ->andReturnUsing(
                fn (int $limit) => array_map(
                    fn ($i) => [
                        'id'           => (string)(1000 + $i),
                        'user_id'      => (string)(2000 + $i),
                        'user_name'    => "TopStreamer{$i}",
                        'viewer_count' => 1000 * $i,
                        'title'        => "Epic Gaming Session {$i}",
                    ],
                    range(1, $limit)
                )
            );

        $this->twitchManagerMock
            ->shouldReceive('getTopGames')
            ->andReturnUsing(
                fn (int $limit = 10) => array_map(
                    fn ($i) => ['id' => (string) $i, 'name' => "Game{$i}"],
                    range(1, $limit)
                )
            );

        $this->twitchManagerMock
            ->shouldReceive('getTopVideos')
            ->andReturnUsing(
                fn (string $gameId, int $limit) => [
                    [
                        'user_name'  => "User{$gameId}",
                        'view_count' => 1000,
                        'title'      => "Top video {$gameId}",
                        'duration'   => '1h',
                        'created_at' => '2020-01-01 00:00:00',
                    ],
                ]
            );

        $this->twitchManagerMock
            ->shouldReceive('getUserById')
            ->andReturnUsing(function (string $userId) {
                if ($userId === '9999') {
                    return [];
                }
                return [[
                    'id'                => $userId,
                    'login'             => "login{$userId}",
                    'display_name'      => "Display {$userId}",
                    'type'              => '',
                    'broadcaster_type'  => 'partner',
                    'description'       => 'Fake streamer for tests',
                    'profile_image_url' => 'https://example.com/profile.png',
                    'offline_image_url' => 'https://example.com/offline.png',
                    'view_count'        => 1_000_000,
                    'created_at'        => '2020-01-01T00:00:00Z',
                ]];
            });

        $this->twitchManagerMock->shouldReceive()->byDefault()->andReturn([]);

        $this->app->instance('App\\Manager\\TwitchManager', $this->twitchManagerMock);
    }
}
