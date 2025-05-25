<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use stdClass;

class DatabaseRepository
{
    public function getUserByEmail(string $email): ?stdClass
    {
        return DB::table('users')
            ->where('email', $email)
            ->first();
    }

    public function registerEmailAndApiKey(string $email, string $apiKey): void
    {
        $user = $this->getUserByEmail($email);
        if ($user) {
            $this->updateApiKey($email, $apiKey);
            return;
        }
        DB::table('users')
            ->insert(['email' => $email, 'api_key' => $apiKey]);
    }

    public function updateApiKey(string $email, string $apiKey): void
    {
        DB::table('users')
            ->where('email', $email)
            ->update(['api_key' => $apiKey]);
    }

    public function getUserIdByCredentials(string $email, string $apiKey): ?int
    {
        return DB::table('users')
            ->where(['email' => $email, 'api_key' => $apiKey])
            ->value('id');
    }

    public function getSession(int $userId): ?stdClass
    {
        return DB::table('sessions')
            ->where('user_id', $userId)
            ->first();
    }

    public function registerSession(int $userId, string $token, string $expiresAt): void
    {
        DB::table('sessions')
            ->insert([
                'user_id'    => $userId,
                'token'      => $token,
                'expires_at' => $expiresAt,
            ]);
    }

    public function updateSession(int $userId, string $token, string $expiresAt): void
    {
        DB::table('sessions')
            ->where('user_id', $userId)
            ->update([
                'token'      => $token,
                'expires_at' => $expiresAt,
            ]);
    }

    public function getSessionByToken(string $token): ?stdClass
    {
        return DB::table('sessions')
            ->where('token', $token)
            ->first();
    }
}
