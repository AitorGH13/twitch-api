<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\TokenRepositoryInterface;
use App\Repository\DatabaseTokenRepository;
use App\Interfaces\UserRepositoryInterface;
use App\Repository\DatabaseUserRepository;
use App\Interfaces\TwitchClientInterface;
use App\Clients\HttpTwitchClient;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TokenRepositoryInterface::class,
            DatabaseTokenRepository::class
        );
        $this->app->bind(
            UserRepositoryInterface::class,
            DatabaseUserRepository::class
        );
        $this->app->bind(
            TwitchClientInterface::class,
            HttpTwitchClient::class
        );
    }
}
