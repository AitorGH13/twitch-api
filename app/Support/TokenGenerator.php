<?php

namespace App\Support;

use Random\RandomException;

final class TokenGenerator
{
    /**
     * @throws RandomException
     */
    public function generate(): string
    {
        return bin2hex(random_bytes(16));
    }
}
