<?php

namespace App\Services;

use App\Exceptions\NoGamesFoundException;
use App\Exceptions\NoVideosFoundException;
use App\Exceptions\UnauthorizedException;
use App\Manager\TwitchManager;
use App\Repository\TopOfTheTopsRepository;
use DateTime;

class TopOfTheTopsService
{
    private TopOfTheTopsRepository $repo;
    private AuthService $authService;
    private TwitchManager $twitchClient;

    public function __construct(
        TopOfTheTopsRepository $repo,
        AuthService $authService,
        TwitchManager $twitchClient
    ) {
        $this->repo = $repo;
        $this->authService = $authService;
        $this->twitchClient = $twitchClient;
    }

    public function getTopOfTheTops(array $input): array
    {
        [$token, $since] = $input;

        if (! $this->authService->validateAccessToken($token)) {
            throw new UnauthorizedException();
        }

        $meta = $this->repo->getCacheMeta();
        $now = time();

        if (
            $meta
            && strtotime($meta->expires_at) >= $now
            && $since === null
        ) {
            return $this->repo->all();
        }

        $this->repo->clearCache();

        $ttl = $since ?? 600;
        $expiresAt = (new DateTime())->modify("+{$ttl} seconds")->format('Y-m-d H:i:s');

        $games = $this->twitchClient->getTopGames(1);
        if (empty($games)) {
            throw new NoGamesFoundException();
        }

        $response = [];
        foreach ($games as $game) {
            $videos = $this->twitchClient->getTopVideos($game['id'], 40);
            if (empty($videos)) {
                throw new NoVideosFoundException();
            }

            $firstVideo = $videos[0];
            $userName = $firstVideo['user_name'];
            $userVideos = array_filter(
                $videos,
                fn (array $video): bool => $video['user_name'] === $userName
            );

            $mysqlCreatedAt = (new DateTime($firstVideo['created_at']))
                ->format('Y-m-d H:i:s');

            $row = [
                'game_id'                => $game['id'],
                'game_name'              => $game['name'],
                'user_name'              => $userName,
                'total_videos'           => count($userVideos),
                'total_views'            => array_sum(array_column($userVideos, 'view_count')),
                'most_viewed_title'      => $firstVideo['title'],
                'most_viewed_views'      => $firstVideo['view_count'],
                'most_viewed_duration'   => $firstVideo['duration'],
                'most_viewed_created_at' => $mysqlCreatedAt,
            ];

            $this->repo->insert($row, $expiresAt);
            $response[] = $row;
        }

        return $response;
    }
}
