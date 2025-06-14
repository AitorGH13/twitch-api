<?php

namespace App\Services;

use App\Exceptions\UnauthorizedException;
use App\Interfaces\TwitchClientInterface;

readonly class StreamsService
{
    public function __construct(
        private AuthService $authService,
        private TwitchClientInterface $twitchClient
    ) {
    }

    /**
     * Valída token y devuelve lista de streams formateados.
     *
     * @param string $token
     * @return array<int,array{title:string,user_name:string}>
     * @throws UnauthorizedException
     */
    public function getLiveStreams(string $token, int $limit): array
    {
        if (! $this->authService->validateAccessToken($token)) {
            throw new UnauthorizedException();
        }

        $data = $this->twitchClient->getLiveStreams($limit);
        return array_map(fn ($stream) => [
            'title'     => $stream['title'],
            'user_name' => $stream['user_name'],
        ], $data);
    }
}
