<?php

namespace LaravelEnso\Upgrade\Contracts;

use Illuminate\Support\Collection;

interface MigratesStructure
{
    public function permissions(): Collection;

    public function roles(): Collection;
}
