<?php // app/Exceptions/UnauthorizedException.php
namespace App\Exceptions;

class UnauthorizedException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Unauthorized. Token is invalid or expired.');
    }
}
