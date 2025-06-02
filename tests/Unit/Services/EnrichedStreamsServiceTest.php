<?php

namespace Unit\Services;

use App\Exceptions\InvalidLimitException;
use App\Exceptions\UnauthorizedException;
use App\Services\AuthService;
use App\Services\EnrichedStreamsService;
use App\Interfaces\TwitchClientInterface;
use Mockery;
use Unit\BaseUnitTestCase;

class EnrichedStreamsServiceTest extends BaseUnitTestCase
{
    /** @test */
    public function invalidTokenThrowsUnauthorizedException()
    {
        $auth = $this->mock(AuthService::class);
        $auth->shouldReceive('validateAccessToken')
            ->once()->with('badToken')->andReturnFalse();

        $client = $this->mock(TwitchClientInterface::class);
        $client->shouldNotReceive('getStreams');

        $service = new EnrichedStreamsService($auth, $client);

        $this->expectException(UnauthorizedException::class);
        $service->getTopEnrichedStreams(5, 'badToken');
    }

    /** @test */
    public function nonPositiveLimitThrowsInvalidLimitException()
    {
        $auth = $this->mock(AuthService::class);
        $auth->shouldReceive('validateAccessToken')
            ->once()->with('goodToken')->andReturnTrue();

        $client = $this->mock(TwitchClientInterface::class);
        $client->shouldNotReceive('getStreams');

        $service = new EnrichedStreamsService($auth, $client);

        $this->expectException(InvalidLimitException::class);
        $service->getTopEnrichedStreams(0, 'goodToken');
    }

    /** @test */
    public function validRequestReturnsEnrichedStreams()
    {
        $auth = $this->mock(AuthService::class);
        $auth->shouldReceive('validateAccessToken')
            ->once()->with('validToken')->andReturnTrue();

        $client = $this->mock(TwitchClientInterface::class);

        $client->shouldReceive('getStreams')
            ->once()->with(2)->andReturn([
                ['id' => '11', 'user_id' => '100', 'user_name' => 'test1',
                    'viewer_count' => 10, 'title' => 'Title1'],
                ['id' => '22', 'user_id' => '200', 'user_name' => 'test2',
                    'viewer_count' => 20, 'title' => 'Title2'],
            ]);

        $client->shouldReceive('getUserById')
            ->once()->with('100')->andReturn([
                ['display_name' => 'Test1', 'profile_image_url' => 'urlA']
            ]);
        $client->shouldReceive('getUserById')
            ->once()->with('200')->andReturn([]);

        $service = new EnrichedStreamsService($auth, $client);

        $result = $service->getTopEnrichedStreams(2, 'validToken');

        $this->assertSame([
            [
                'stream_id'         => '11',
                'user_id'           => '100',
                'user_name'         => 'test1',
                'viewer_count'      => 10,
                'title'             => 'Title1',
                'user_display_name' => 'Test1',
                'profile_image_url' => 'urlA',
            ],
            [
                'stream_id'         => '22',
                'user_id'           => '200',
                'user_name'         => 'test2',
                'viewer_count'      => 20,
                'title'             => 'Title2',
                'user_display_name' => '',
                'profile_image_url' => '',
            ],
        ], $result);
    }
}
