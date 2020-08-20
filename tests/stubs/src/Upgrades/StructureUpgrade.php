<?php

namespace LaravelEnso\TestUpgrade\Upgrades;

use Illuminate\Support\Collection;
use LaravelEnso\Upgrade\Contracts\MigratesStructure;

class StructureUpgrade implements MigratesStructure
{
    public function permissions(): Collection
    {
        return new Collection();
    }

    public function roles(): Collection
    {
        return new Collection();
    }
}
