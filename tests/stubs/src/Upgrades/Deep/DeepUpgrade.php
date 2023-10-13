<?php

namespace LaravelLiberu\TestUpgrade\Upgrades\Deep;

use LaravelLiberu\Upgrade\Contracts\Upgrade;

class DeepUpgrade implements Upgrade
{
    public function isMigrated(): bool
    {
        return false;
    }
}
