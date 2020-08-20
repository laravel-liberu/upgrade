<?php

namespace LaravelEnso\TestUpgrade\Upgrades\Deep;

use LaravelEnso\Upgrade\Contracts\Upgrade;

class DeepUpgrade implements Upgrade
{
    public function isMigrated(): bool
    {
        return false;
    }
}
