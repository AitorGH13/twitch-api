<?php

namespace App\Interfaces;

interface TwitchClientInterface
{
    public function getTopGames(int $first): array;

    public function getTopVideos(string $gameId, int $first): array;

    public function getUserById(string $userId): array;

    public function getLiveStreams(int $first): array;

    public function getStreams(int $first): array;
}
