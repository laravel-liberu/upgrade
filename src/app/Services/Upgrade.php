<?php

namespace LaravelEnso\Upgrade\App\Services;

use Illuminate\Support\Collection;
use LaravelEnso\Upgrade\App\Contracts\MigratesStructure;
use LaravelEnso\Upgrade\App\Contracts\Upgrade as Contract;

class Upgrade
{
    private Collection $upgrades;

    public function __construct(array $upgrades)
    {
        $this->upgrades = new Collection($upgrades);
    }

    public function handle()
    {
        $this->upgrades->each(fn ($upgrade) => $this->run(new $upgrade));
    }

    private function run(Contract $upgrade)
    {
        (new Database($this->upgrade($upgrade)))->handle();
    }

    private function upgrade(Contract $upgrade)
    {
        return $upgrade instanceof MigratesStructure
            ? new Structure($upgrade)
            : $upgrade;
    }
}
