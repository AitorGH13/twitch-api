<?php // app/Exceptions/NoVideosFoundException.php
namespace App\Exceptions;

class NoVideosFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('No videos found.');
    }
}
