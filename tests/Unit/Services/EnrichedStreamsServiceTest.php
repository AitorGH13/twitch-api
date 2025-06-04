<?php

namespace Unit\Services;

use App\Exceptions\InvalidLimitException;
use App\Exceptions\UnauthorizedException;
use App\Services\AuthService;
use App\Services\EnrichedStreamsService;
use App\Interfaces\TwitchClientInterface;
use Unit\BaseUnitTestCase;

class EnrichedStreamsServiceTest extends BaseUnitTestCase
{
    /** @test */
    public function invalidTokenThrowsUnauthorizedException()
    {
        $authMock = $this->mock(AuthService::class);
        $clientMock = $this->mock(TwitchClientInterface::class);

        $badToken = 'badToken';
        $limit = 5;

        $authMock->shouldReceive('validateAccessToken')
            ->once()
            ->with($badToken)
            ->andReturnFalse();

        $clientMock->shouldNotReceive('getStreams');

        $service = new EnrichedStreamsService(
            $authMock,
            $clientMock
        );

        $this->expectException(UnauthorizedException::class);
        $service->getTopEnrichedStreams($limit, $badToken);
    }

    /** @test */
    public function nonPositiveLimitThrowsInvalidLimitException()
    {
        $authMock = $this->mock(AuthService::class);
        $clientMock = $this->mock(TwitchClientInterface::class);

        $goodToken = 'goodToken';
        $zeroLimit = 0;

        $authMock->shouldReceive('validateAccessToken')
            ->once()
            ->with($goodToken)
            ->andReturnTrue();

        $clientMock->shouldNotReceive('getStreams');

        $service = new EnrichedStreamsService(
            $authMock,
            $clientMock
        );

        $this->expectException(InvalidLimitException::class);
        $service->getTopEnrichedStreams($zeroLimit, $goodToken);
    }

    /** @test */
    public function validRequestReturnsEnrichedStreams()
    {
        $authMock = $this->mock(AuthService::class);
        $clientMock = $this->mock(TwitchClientInterface::class);

        $goodToken = 'validToken';
        $limit = 2;

        $streamsMock = [
            [
                'id'            => '11',
                'user_id'       => '100',
                'user_name'     => 'test1',
                'viewer_count'  => 10,
                'title'         => 'Title1'
            ],
            [
                'id'            => '22',
                'user_id'       => '200',
                'user_name'     => 'test2',
                'viewer_count'  => 20,
                'title'         => 'Title2'
            ],
        ];

        $userData100Mock = [
            ['display_name' => 'Test1', 'profile_image_url' => 'urlA']
        ];

        $userData200Mock = [];

        $expectedStreams = [
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
        ];

        $authMock->shouldReceive('validateAccessToken')
            ->once()
            ->with($goodToken)
            ->andReturnTrue();

        $clientMock->shouldReceive('getStreams')
            ->once()
            ->with($limit)
            ->andReturn($streamsMock);

        $clientMock->shouldReceive('getUserById')
            ->once()
            ->with('100')
            ->andReturn($userData100Mock);

        $clientMock->shouldReceive('getUserById')
            ->once()
            ->with('200')
            ->andReturn($userData200Mock);

        $service = new EnrichedStreamsService(
            $authMock,
            $clientMock
        );

        $actualStreams = $service->getTopEnrichedStreams(
            $limit,
            $goodToken
        );

        $this->assertSame($expectedStreams, $actualStreams);
    }
}
