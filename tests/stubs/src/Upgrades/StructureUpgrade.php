<?php

namespace LaravelLiberu\TestUpgrade\Upgrades;

use LaravelLiberu\Upgrade\Contracts\MigratesStructure;

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
