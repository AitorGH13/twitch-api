<?php // app/Services/UserService.php
namespace App\Services;

use App\Repository\UserRepository;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UnauthorizedException;
use DateTime;

class UserService
{
    public function __construct(
        private UserRepository  $repo,
        private AuthService     $authService,
        private TwitchApiClient $twitchClient
    ) {}

    public function get(array $input): array
    {
        [$id, $token] = $input;

        if (! $this->authService->validateAccessToken($token)) {
            throw new UnauthorizedException();
        }

        // 1) Busca en tabla cache
        $cached = $this->repo->findById($id);
        if ($cached) {
            return $cached;
        }

        // 2) Llama a Twitch API
        $data = $this->twitchClient->getUserById($id);
        if (empty($data)) {
            throw new UserNotFoundException();
        }

        $user = $data[0];
        // Convertimos created_at de ISO8601 a Y-m-d H:i:s
        $user['created_at'] = (new \DateTime($user['created_at']))
            ->format('Y-m-d H:i:s');
        // 3) Inserta en cache
        $this->repo->insert($user);

        return $user;
    }
}
