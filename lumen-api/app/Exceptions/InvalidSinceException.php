<?php

namespace App\Exceptions;

use RuntimeException;

class InvalidSinceException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("Invalid 'since' parameter.");
    }
}
