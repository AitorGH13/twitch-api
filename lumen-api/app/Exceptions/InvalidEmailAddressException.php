<?php

namespace App\Exceptions;

class InvalidEmailAddressException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('The email must be a valid email address');
    }
}
