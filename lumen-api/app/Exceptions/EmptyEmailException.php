<?php

namespace App\Exceptions;

use RuntimeException;

class EmptyEmailException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('The email is mandatory');
    }
}
