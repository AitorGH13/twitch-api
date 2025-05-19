<?php // app/Services/StreamsService.php
namespace App\Services;

use App\Exceptions\UnauthorizedException;
use App\Manager\TwitchManager;

class StreamsService
{
    public function __construct(
        private AuthService        $authService,
        private TwitchManager $twitchClient
    ) {}

    /**
     * Valida token y devuelve lista de streams formateados.
     *
     * @param string $token
     * @return array<int,array{title:string,user_name:string}>
     * @throws UnauthorizedException
     */
    public function getLiveStreams(string $token): array
    {
        if (! $this->authService->validateAccessToken($token)) {
            throw new UnauthorizedException();
        }

        $data = $this->twitchClient->getLiveStreams();
        return array_map(fn($s) => [
            'title'     => $s['title'],
            'user_name' => $s['user_name'],
        ], $data);
    }
}
