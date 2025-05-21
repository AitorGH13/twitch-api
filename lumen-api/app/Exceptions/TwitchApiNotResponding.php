<?php

namespace App\Exceptions;

class TwitchApiNotResponding extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Twitch API is not responding.');
    }
}
