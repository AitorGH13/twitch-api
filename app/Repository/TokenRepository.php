<?php

namespace App\Repository;

use App\Interfaces\TokenRepositoryInterface;
use App\Domain\Token;
use Exception;
use Illuminate\Support\Facades\DB;
use DateTimeImmutable;

final class TokenRepository implements TokenRepositoryInterface
{
    /**
     * @throws Exception
     */
    public function findActiveByUserId(int $userId): ?Token
    {
        $row = DB::table('sessions')
            ->where('user_id', $userId)
            ->where('expires_at', '>=', new DateTimeImmutable())
            ->first();

        return $row ? $this->map($row) : null;
    }

    /**
     * @throws Exception
     */
    public function findByValue(string $value): ?Token
    {
        $row = DB::table('sessions')
            ->where('token', $value)
            ->first();

        return $row ? $this->map($row) : null;
    }

    public function save(Token $token): void
    {
        DB::table('sessions')->updateOrInsert(
            ['user_id' => $token->userId],
            [
                'token'      => $token->value,
                'expires_at' => $token->expiresAt->format('Y-m-d H:i:s'),
            ]
        );
    }

    /**
     * @throws Exception
     */
    private function map(object $row): Token
    {
        return new Token(
            $row->token,
            $row->user_id,
            new DateTimeImmutable($row->expires_at),
        );
    }
}
