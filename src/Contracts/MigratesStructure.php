<?php

namespace LaravelEnso\Upgrade\Contracts;

use Illuminate\Support\Collection;

interface MigratesStructure
{
    public function permissions(): array;

    public function roles(): array;
}
