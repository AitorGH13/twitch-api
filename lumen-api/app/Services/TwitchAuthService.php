<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TwitchAuthService
{
    /**
     * Obtiene (y cachea) un token de aplicación válido.
     */
    public function getAppAccessToken(): string
    {
        // TTL en segundos (por defecto 1 hora)
        $ttl = env('TWITCH_TOKEN_TTL', 3600);

        return Cache::remember('twitch_app_token', $ttl - 60, function() {
            $response = Http::asForm()->post(
                'https://id.twitch.tv/oauth2/token',
                [
                    'client_id'     => env('TWITCH_CLIENT_ID'),
                    'client_secret' => env('TWITCH_CLIENT_SECRET'),
                    'grant_type'    => 'client_credentials',
                ]
            );

            if (! $response->ok() || ! isset($response['access_token'])) {
                throw new \RuntimeException('Could not fetch Twitch access token');
            }

            return $response['access_token'];
        });
    }
}
