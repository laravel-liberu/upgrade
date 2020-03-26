<?php

namespace LaravelEnso\Upgrade\App\Traits;

use Illuminate\Support\Collection;

trait StructureMigration
{
    public function permissions(): Collection
    {
        return new Collection($this->permissions);
    }

    public function roles(): Collection
    {
        return new Collection($this->roles ?? []);
    }
}
