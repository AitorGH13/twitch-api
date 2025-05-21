<?php

// app/Repository/UserRepository.php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use stdClass;

class UserRepository
{
    public function findById(string $userId): ?array
    {
        $row = DB::table('streamers')
            ->where('id', $userId)
            ->first();

        return $row ? (array)$row : null;
    }

    public function insert(array $user): void
    {
        DB::table('streamers')->insert([
            'id'               => $user['id'],
            'login'            => $user['login'],
            'display_name'     => $user['display_name'],
            'type'             => $user['type'],
            'broadcaster_type' => $user['broadcaster_type'],
            'description'      => $user['description'],
            'profile_image_url' => $user['profile_image_url'],
            'offline_image_url' => $user['offline_image_url'],
            'view_count'       => $user['view_count'],
            'created_at'       => $user['created_at'],
        ]);
    }
}
