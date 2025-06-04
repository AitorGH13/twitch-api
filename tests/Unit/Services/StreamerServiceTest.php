<?php

namespace Unit\Services;

use App\Exceptions\UnauthorizedException;
use App\Exceptions\UserNotFoundException;
use App\Services\AuthService;
use App\Services\StreamerService;
use App\Interfaces\TwitchClientInterface;
use App\Interfaces\StreamerRepositoryInterface;
use Unit\BaseUnitTestCase;

class StreamerServiceTest extends BaseUnitTestCase
{
    /** @test */
    public function invalidTokenThrowsUnauthorized()
    {
        $repoMock = $this->mock(StreamerRepositoryInterface::class);
        $authMock = $this->mock(AuthService::class);
        $clientMock = $this->mock(TwitchClientInterface::class);

        $testUserId = 123;
        $badToken = 'badToken';
        $params = [$testUserId, $badToken];

        $authMock->shouldReceive('validateAccessToken')
            ->once()
            ->with($badToken)
            ->andReturnFalse();

        $repoMock->shouldNotReceive('findById');

        $service = new StreamerService(
            $repoMock,
            $authMock,
            $clientMock
        );

        $this->expectException(UnauthorizedException::class);
        $service->getUserProfile($params);
    }

    /** @test */
    public function withoutCallingApiReturnsCachedProfile()
    {
        $userId = 42;
        $goodToken = 'goodToken';
        $cachedProfile = [
            'id'        => $userId,
            'user_name' => 'test'
        ];

        $params = [$userId, $goodToken];

        $repoMock = $this->mock(StreamerRepositoryInterface::class);
        $authMock = $this->mock(AuthService::class);
        $clientMock = $this->mock(TwitchClientInterface::class);

        $authMock->shouldReceive('validateAccessToken')
            ->once()
            ->with($goodToken)
            ->andReturnTrue();

        $repoMock->shouldReceive('findById')
            ->once()
            ->with($userId)
            ->andReturn($cachedProfile);

        $clientMock->shouldNotReceive('getUserById');
        $repoMock->shouldNotReceive('insert');

        $service = new StreamerService(
            $repoMock,
            $authMock,
            $clientMock
        );

        $result = $service->getUserProfile($params);

        $this->assertSame($cachedProfile, $result);
    }

    /** @test */
    public function whenApiReturnsEmptyThrowsUserNotFoundException()
    {
        $userId = 999;
        $goodToken = 'goodToken';
        $params = [$userId, $goodToken];

        $repoMock = $this->mock(StreamerRepositoryInterface::class);
        $authMock = $this->mock(AuthService::class);
        $clientMock = $this->mock(TwitchClientInterface::class);

        $authMock->shouldReceive('validateAccessToken')
            ->once()
            ->with($goodToken)
            ->andReturnTrue();

        $repoMock->shouldReceive('findById')
            ->once()
            ->with($userId)
            ->andReturnNull();

        $clientMock->shouldReceive('getUserById')
            ->once()
            ->with($userId)
            ->andReturn([]);

        $repoMock->shouldNotReceive('insert');

        $service = new StreamerService(
            $repoMock,
            $authMock,
            $clientMock
        );

        $this->expectException(UserNotFoundException::class);
        $service->getUserProfile($params);
    }

    /** @test */
    public function notCachedFetchesFormatsAndCachesProfile()
    {
        $userId = 100;
        $goodToken = 'goodToken';
        $params = [$userId, $goodToken];

        $apiResponse = [
            'id'                => (string) $userId,
            'user_name'         => 'test',
            'display_name'      => 'Test',
            'created_at'        => '2024-01-15T12:34:56Z',
            'profile_image_url' => 'img',
        ];

        $formattedProfile = $apiResponse;
        $formattedProfile['created_at'] = '2024-01-15 12:34:56';

        $repoMock = $this->mock(StreamerRepositoryInterface::class);
        $authMock = $this->mock(AuthService::class);
        $clientMock = $this->mock(TwitchClientInterface::class);

        $authMock->shouldReceive('validateAccessToken')
            ->once()
            ->with($goodToken)
            ->andReturnTrue();

        $repoMock->shouldReceive('findById')
            ->once()
            ->with($userId)
            ->andReturnNull();

        $clientMock->shouldReceive('getUserById')
            ->once()
            ->with($userId)
            ->andReturn([$apiResponse]);

        $repoMock->shouldReceive('insert')
            ->once()
            ->with($formattedProfile);

        $service = new StreamerService(
            $repoMock,
            $authMock,
            $clientMock
        );

        $retrieved = $service->getUserProfile($params);

        $this->assertSame($formattedProfile, $retrieved);
    }
}
