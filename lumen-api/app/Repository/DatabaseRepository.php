<?php
// app/Repository/DatabaseRepository.php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use stdClass;

class DatabaseRepository
{
    public function getUserByEmail(string $email): ?stdClass
    {
        return DB::table('users')
            ->where('email', $email)
            ->first();               // devuelve null o un objeto stdClass
    }

    public function registerEmailAndApiKey(string $email, string $apiKey): void
    {
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
            ->value('id');         // devuelve null o el id
    }

    public function getSession(int $userId): ?stdClass
    {
        return DB::table('sessions')
            ->where('user_id', $userId)
            ->first();
    }

    public function registerSession(int $userId, string $token, int $expiresAt): void
    {
        DB::table('sessions')
            ->insert([
                'user_id'    => $userId,
                'token'      => $token,
                'expires_at' => date('Y-m-d H:i:s', $expiresAt),
            ]);
    }

    public function updateSession(int $userId, string $token, int $expiresAt): void
    {
        DB::table('sessions')
            ->where('user_id', $userId)
            ->update([
                'token'      => $token,
                'expires_at' => date('Y-m-d H:i:s', $expiresAt),
            ]);
    }
}
