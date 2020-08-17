<?php

namespace LaravelEnso\TestUpgrade\Upgrades;

use LaravelEnso\Upgrade\Contracts\Upgrade;

class SimpleUpgrade implements Upgrade
{
    public function isMigrated(): bool
    {
        return false;
    }
}
