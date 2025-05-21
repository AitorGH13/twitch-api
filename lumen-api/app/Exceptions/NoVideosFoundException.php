<?php

namespace App\Exceptions;

use RuntimeException;

class NoVideosFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('No videos found.');
    }
}
