<?php

namespace App\Exceptions;

use RuntimeException;

class EmptyTokenException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invalid parameter: token is required');
    }
}
