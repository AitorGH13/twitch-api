<?php

namespace App\Services;

use App\Exceptions\UnauthorizedException;
use App\Exceptions\UserNotFoundException;
use App\Manager\TwitchManager;
use App\Repository\UserRepository;
use DateTime;
use Exception;

class UserService
{
    private UserRepository $repo;
    private AuthService $authService;
    private TwitchManager $twitchClient;

    public function __construct(
        UserRepository $repo,
        AuthService $authService,
        TwitchManager $twitchClient
    ) {
        $this->repo = $repo;
        $this->authService = $authService;
        $this->twitchClient = $twitchClient;
    }

    /**
     * Obtiene el perfil de usuario, validando token y cacheando resultados.
     *
     * @param array $input [0 => int userId, 1 => string accessToken]
     *
     * @return array
     * @throws UserNotFoundException
     *
     * @throws UnauthorizedException
     * @throws Exception
     */
    public function getUserProfile(array $input): array
    {
        [$userId, $accessToken] = $input;

        if (! $this->authService->validateAccessToken($accessToken)) {
            throw new UnauthorizedException();
        }

        $cachedProfile = $this->repo->findById($userId);
        if ($cachedProfile) {
            return $cachedProfile;
        }

        $apiResponse = $this->twitchClient->getUserById($userId);
        if (empty($apiResponse)) {
            throw new UserNotFoundException();
        }

        $profile = $apiResponse[0];
        $profile['created_at'] = (new DateTime($profile['created_at']))
            ->format('Y-m-d H:i:s');

        $this->repo->insert($profile);

        return $profile;
    }
}
