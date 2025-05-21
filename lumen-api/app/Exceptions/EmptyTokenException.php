<?php

// app/Exceptions/EmptyTokenException.php

namespace App\Exceptions;

class EmptyTokenException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invalid parameter: token is required');
    }
}
