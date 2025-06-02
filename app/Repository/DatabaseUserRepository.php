<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use stdClass;
use App\Interfaces\UserRepositoryInterface;

final class DatabaseUserRepository implements UserRepositoryInterface
{
    public function getByEmail(string $email): ?stdClass
    {
        return DB::table('users')
            ->where('email', $email)
            ->first();
    }

    public function register(string $email, string $apiKey): void
    {
        DB::table('users')->updateOrInsert(
            ['email'   => $email],
            ['api_key' => $apiKey]
        );
    }

    public function updateApiKey(string $email, string $apiKey): void
    {
        DB::table('users')
            ->where('email', $email)
            ->update(['api_key' => $apiKey]);
    }

    public function idByCredentials(string $email, string $apiKey): ?int
    {
        return DB::table('users')
            ->where(['email' => $email, 'api_key' => $apiKey])
            ->value('id');
    }
}
