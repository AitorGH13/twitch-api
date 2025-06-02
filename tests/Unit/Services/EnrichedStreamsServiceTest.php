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
    public function tokenIsInvalidThrowsUnauthorized()
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
    public function limitIsNonPositiveThrowsInvalidLimit()
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
    public function everythingIsOkReturnsEnrichedStreams()
    {
        $auth = $this->mock(AuthService::class);
        $auth->shouldReceive('validateAccessToken')
            ->once()->with('valid')->andReturnTrue();

        $client = $this->mock(TwitchClientInterface::class);

        $client->shouldReceive('getStreams')
            ->once()->with(2)->andReturn([
                ['id' => '11', 'user_id' => '100', 'user_name' => 'alice',
                    'viewer_count' => 10, 'title' => 'Foo'],
                ['id' => '22', 'user_id' => '200', 'user_name' => 'bob',
                    'viewer_count' => 20, 'title' => 'Bar'],
            ]);

        $client->shouldReceive('getUserById')
            ->once()->with('100')->andReturn([
                ['display_name' => 'Alice', 'profile_image_url' => 'urlA']
            ]);
        $client->shouldReceive('getUserById')
            ->once()->with('200')->andReturn([]); // user no encontrado

        $service = new EnrichedStreamsService($auth, $client);

        $result = $service->getTopEnrichedStreams(2, 'valid');

        $this->assertSame([
            [
                'stream_id'         => '11',
                'user_id'           => '100',
                'user_name'         => 'alice',
                'viewer_count'      => 10,
                'title'             => 'Foo',
                'user_display_name' => 'Alice',
                'profile_image_url' => 'urlA',
            ],
            [
                'stream_id'         => '22',
                'user_id'           => '200',
                'user_name'         => 'bob',
                'viewer_count'      => 20,
                'title'             => 'Bar',
                'user_display_name' => '',
                'profile_image_url' => '',
            ],
        ], $result);
    }
}
