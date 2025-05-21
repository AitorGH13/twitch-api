<?php

// app/Exceptions/InvalidApiKeyException.php

namespace App\Exceptions;

class InvalidApiKeyException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Unauthorized. API access token is invalid.');
    }
}
