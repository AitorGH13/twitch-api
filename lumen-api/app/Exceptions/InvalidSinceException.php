<?php

// app/Exceptions/InvalidSinceException.php

namespace App\Exceptions;

class InvalidSinceException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct("Invalid 'since' parameter.");
    }
}
