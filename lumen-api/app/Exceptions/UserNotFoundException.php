<?php // app/Exceptions/UserNotFoundException.php
namespace App\Exceptions;

class UserNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('User not found.');
    }
}
