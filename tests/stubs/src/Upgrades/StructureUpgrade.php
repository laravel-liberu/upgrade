<?php

namespace LaravelEnso\TestUpgrade\Upgrades;

use LaravelEnso\Upgrade\Contracts\MigratesStructure;

class StructureUpgrade implements MigratesStructure
{
    public function permissions(): array
    {
        return [];
    }

    public function roles(): array
    {
        return [];
    }
}
