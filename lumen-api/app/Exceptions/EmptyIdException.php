<?php

namespace App\Exceptions;

use RuntimeException;

class EmptyIdException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("Invalid or missing 'id' parameter.");
    }
}
