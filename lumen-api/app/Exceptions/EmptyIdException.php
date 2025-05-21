<?php

// app/Exceptions/EmptyIdException.php

namespace App\Exceptions;

class EmptyIdException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct("Invalid or missing 'id' parameter.");
    }
}
