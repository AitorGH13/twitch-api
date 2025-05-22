<?php

namespace App\Exceptions;

use RuntimeException;

class EmptyApiKeyException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('The api_key is mandatory');
    }
}
