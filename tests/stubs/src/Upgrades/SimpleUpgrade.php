<?php

namespace LaravelLiberu\TestUpgrade\Upgrades;

use LaravelLiberu\Upgrade\Contracts\Upgrade;

class SimpleUpgrade implements Upgrade
{
    public function isMigrated(): bool
    {
        return false;
    }
}
