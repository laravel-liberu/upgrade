<?php

namespace LaravelEnso\Upgrade\App\Contracts;

interface Upgrade
{
    public function isMigrated(): bool;
}
