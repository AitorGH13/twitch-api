<?php

namespace App\Exceptions;

use RuntimeException;

class NoGamesFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('No games found.');
    }
}
