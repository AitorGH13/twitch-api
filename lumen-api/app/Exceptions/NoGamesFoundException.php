<?php // app/Exceptions/NoGamesFoundException.php
namespace App\Exceptions;

class NoGamesFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('No games found.');
    }
}
