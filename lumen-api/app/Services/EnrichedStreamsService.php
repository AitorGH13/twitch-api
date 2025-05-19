<?php // app/Services/EnrichedStreamsService.php
namespace App\Services;

use App\Exceptions\InvalidLimitException;
use App\Exceptions\UnauthorizedException;

class EnrichedStreamsService
{
    public function __construct(
        private AuthService     $authService,
        private TwitchApiClient $twitchClient
    ) {}

    /**
     * @return array<int,array{
     *   stream_id: string,
     *   user_id: string,
     *   user_name: string,
     *   viewer_count: int,
     *   title: string,
     *   user_display_name: string,
     *   profile_image_url: string
     * }>
     * @throws UnauthorizedException|InvalidLimitException
     */
    public function getTopEnrichedStreams(int $limit, string $token): array
    {
        if (! $this->authService->validateAccessToken($token)) {
            throw new UnauthorizedException();
        }

        if ($limit <= 0) {
            throw new InvalidLimitException();
        }

        $streams = $this->twitchClient->getStreams($limit);

        $enriched = [];
        foreach ($streams as $s) {
            $userData = $this->twitchClient->getUserById($s['user_id'])[0] ?? null;
            $enriched[] = [
                'stream_id'         => $s['id'],
                'user_id'           => $s['user_id'],
                'user_name'         => $s['user_name'],
                'viewer_count'      => $s['viewer_count'],
                'title'             => $s['title'],
                'user_display_name' => $userData['display_name'] ?? '',
                'profile_image_url' => $userData['profile_image_url'] ?? '',
            ];
        }

        return $enriched;
    }
}
