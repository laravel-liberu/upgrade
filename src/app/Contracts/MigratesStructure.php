<?php

namespace LaravelEnso\Upgrade\App\Contracts;

use Illuminate\Support\Collection;

interface MigratesStructure
{
    public function permissions(): Collection;

    public function roles(): Collection;
}
