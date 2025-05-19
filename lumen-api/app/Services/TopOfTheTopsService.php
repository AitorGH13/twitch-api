<?php // app/Services/TopOfTheTopsService.php
namespace App\Services;

use App\Exceptions\NoGamesFoundException;
use App\Exceptions\NoVideosFoundException;
use App\Exceptions\UnauthorizedException;
use App\Manager\TwitchManager;
use App\Repository\TopOfTheTopsRepository;
use DateTime;

class TopOfTheTopsService
{
    public function __construct(
        private TopOfTheTopsRepository $repo,
        private AuthService            $authService,
        private TwitchManager $twitchClient
    ) {}

    public function get(array $input): array
    {
        [$token, $since] = $input;

        // 1) valida token
        if (! $this->authService->validateAccessToken($token)) {
            throw new UnauthorizedException();
        }

        // 2) comprueba caché
        $meta = $this->repo->getCacheMeta();
        $now = time();
        if (! $meta
            || strtotime($meta->expires_at) < $now
            || $since !== null
        ) {
            // refresca
            $this->repo->clearCache();

            // duración por defecto 600s
            $ttl = $since ?? 600;
            $expiresAt = (new DateTime("+{$ttl} seconds"))->format('Y-m-d H:i:s');

            $games = $this->twitchClient->getTopGames(3);
            if (empty($games)) {
                throw new NoGamesFoundException();
            }

            $response = [];
            foreach ($games as $game) {
                $videos = $this->twitchClient->getTopVideos($game['id'], 40);
                if (empty($videos)) {
                    throw new NoVideosFoundException();
                }
                // metrics
                $first   = $videos[0];
                $user    = $first['user_name'];
                $userSet = array_filter($videos, fn($v) => $v['user_name'] === $user);
                $isoCreatedAt = $first['created_at'];  // p.e. "2024-11-28T02:06:07Z"
                $mysqlCreatedAt = (new \DateTime($isoCreatedAt))
                    ->format('Y-m-d H:i:s');
                $row = [
                    'game_id'                => $game['id'],
                    'game_name'              => $game['name'],
                    'user_name'              => $user,
                    'total_videos'           => count($userSet),
                    'total_views'            => array_sum(array_column($userSet,'view_count')),
                    'most_viewed_title'      => $first['title'],
                    'most_viewed_views'      => $first['view_count'],
                    'most_viewed_duration'   => $first['duration'],
                    'most_viewed_created_at' => $mysqlCreatedAt,
                ];
                $this->repo->insert($row, $expiresAt);
                $response[] = $row;
            }

            return $response;
        }

        // 3) devuelve cacheada
        return $this->repo->all();
    }
}
