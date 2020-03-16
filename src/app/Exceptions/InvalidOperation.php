<?php

namespace LaravelEnso\Upgrade\App\Exceptions;

use Exception;

class InvalidOperation extends Exception
{
    public function rollback()
    {
        return new self('Missing rollback scenario');
    }
}
