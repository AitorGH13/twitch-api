<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\StreamerRepositoryInterface;
use App\Repository\StreamerRepository;
use App\Interfaces\TokenRepositoryInterface;
use App\Repository\TokenRepository;
use App\Interfaces\UserRepositoryInterface;
use App\Repository\UserRepository;
use App\Interfaces\TwitchClientInterface;
use App\Clients\HttpTwitchClient;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            StreamerRepositoryInterface::class,
            StreamerRepository::class
        );
        $this->app->bind(
            TokenRepositoryInterface::class,
            TokenRepository::class
        );
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            TwitchClientInterface::class,
            HttpTwitchClient::class
        );
    }
}
