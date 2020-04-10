<?php

namespace LaravelEnso\Upgrade\App\Exceptions;

use Exception;

class InvalidOperation extends Exception
{
    public static function rollback()
    {
        return new static('Missing rollback scenario');
    }
}
