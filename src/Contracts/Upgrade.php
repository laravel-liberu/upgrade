<?php

namespace LaravelEnso\Upgrade\Contracts;

interface Upgrade
{
    public function isMigrated(): bool;
}
