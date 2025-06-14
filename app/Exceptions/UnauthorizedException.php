<?php

namespace App\Exceptions;

use RuntimeException;

class UnauthorizedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Unauthorized. Twitch access token is invalid or has expired.');
    }
}
