<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class TwitchApiException extends RuntimeException
{
    /**
     * @param string         $message  Custom error message
     * @param int            $code     Error code (optional)
     * @param Throwable|null $previous Previous exception (optional)
     */
    public function __construct(
        string $message = 'Twitch API exception.',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
