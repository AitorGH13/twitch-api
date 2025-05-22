<?php

namespace App\Exceptions;

use RuntimeException;

class InvalidApiKeyException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Unauthorized. API access token is invalid.');
    }
}
