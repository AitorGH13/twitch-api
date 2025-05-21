<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Client\Factory as HttpClient;
use RuntimeException;

class TwitchAuthService
{
    private CacheRepository $cache;
    private HttpClient $httpClient;

    public function __construct(CacheRepository $cache, HttpClient $httpClient)
    {
        $this->cache = $cache;
        $this->httpClient = $httpClient;
    }

    /**
     * Obtiene (y cachea) un token de aplicación válido.
     */
    public function getAppAccessToken(): string
    {
        $ttl = (int) env('TWITCH_TOKEN_TTL', 3600);

        return $this->cache->remember(
            'twitch_app_token',
            $ttl - 60,
            function (): string {
                $response = $this->httpClient
                    ->asForm()
                    ->post(
                        'https://id.twitch.tv/oauth2/token',
                        [
                            'client_id'     => env('TWITCH_CLIENT_ID'),
                            'client_secret' => env('TWITCH_CLIENT_SECRET'),
                            'grant_type'    => 'client_credentials',
                        ]
                    );

                if (! $response->ok() || ! isset($response['access_token'])) {
                    throw new RuntimeException('Could not fetch Twitch access token');
                }

                return $response['access_token'];
            }
        );
    }
}
