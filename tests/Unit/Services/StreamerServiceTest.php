<?php

namespace Unit\Services;

use App\Exceptions\UnauthorizedException;
use App\Exceptions\UserNotFoundException;
use App\Services\AuthService;
use App\Services\StreamerService;
use App\Interfaces\TwitchClientInterface;
use App\Repository\StreamerRepository;
use Unit\BaseUnitTestCase;

class StreamerServiceTest extends BaseUnitTestCase
{
    /** @test */
    public function invalidTokenThrowsUnauthorized()
    {
        $repo        = $this->mock(StreamerRepository::class);
        $auth        = $this->mock(AuthService::class);
        $twitch      = $this->mock(TwitchClientInterface::class);

        $auth->shouldReceive('validateAccessToken')
            ->once()->with('badToken')->andReturnFalse();
        $repo->shouldNotReceive('findById');

        $service = new StreamerService($repo, $auth, $twitch);

        $this->expectException(UnauthorizedException::class);
        $service->getUserProfile([123, 'badToken']);
    }

    /** @test */
    public function withoutCallingApiReturnsCachedProfile()
    {
        $userId   = 42;
        $cached   = ['id' => $userId, 'user_name' => 'test'];

        $repo   = $this->mock(StreamerRepository::class);
        $auth   = $this->mock(AuthService::class);
        $twitch = $this->mock(TwitchClientInterface::class);

        $auth->shouldReceive('validateAccessToken')
            ->once()->with('goodToken')->andReturnTrue();
        $repo->shouldReceive('findById')
            ->once()->with($userId)->andReturn($cached);
        $twitch->shouldNotReceive('getUserById');
        $repo->shouldNotReceive('insert');

        $service = new StreamerService($repo, $auth, $twitch);

        $this->assertSame($cached, $service->getUserProfile([$userId, 'goodToken']));
    }

    /** @test */
    public function apiReturnsEmptyThrowsUserNotFound()
    {
        $userId = 999;

        $repo   = $this->mock(StreamerRepository::class);
        $auth   = $this->mock(AuthService::class);
        $twitch = $this->mock(TwitchClientInterface::class);

        $auth->shouldReceive('validateAccessToken')
            ->once()->with('goodToken')->andReturnTrue();
        $repo->shouldReceive('findById')
            ->once()->with($userId)->andReturnNull();
        $twitch->shouldReceive('getUserById')
            ->once()->with($userId)->andReturn([]);
        $repo->shouldNotReceive('insert');

        $service = new StreamerService($repo, $auth, $twitch);

        $this->expectException(UserNotFoundException::class);
        $service->getUserProfile([$userId, 'goodToken']);
    }

    /** @test */
    public function notCachedFetchesFormatsAndCachesProfile()
    {
        $userId = 100;
        $apiUser = [
            'id'              => (string) $userId,
            'user_name'       => 'test',
            'display_name'    => 'Test',
            'created_at'      => '2024-01-15T12:34:56Z',
            'profile_image_url' => 'img',
        ];

        $expected = $apiUser;
        $expected['created_at'] = '2024-01-15 12:34:56';

        $repo   = $this->mock(StreamerRepository::class);
        $auth   = $this->mock(AuthService::class);
        $twitch = $this->mock(TwitchClientInterface::class);

        $auth->shouldReceive('validateAccessToken')
            ->once()->with('goodToken')->andReturnTrue();
        $repo->shouldReceive('findById')
            ->once()->with($userId)->andReturnNull();
        $twitch->shouldReceive('getUserById')
            ->once()->with($userId)->andReturn([$apiUser]);
        $repo->shouldReceive('insert')
            ->once()->with($expected);

        $service = new StreamerService($repo, $auth, $twitch);

        $profile = $service->getUserProfile([$userId, 'goodToken']);

        $this->assertSame($expected, $profile);
    }
}
