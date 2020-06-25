<?php

namespace LaravelEnso\Upgrade\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use LaravelEnso\Upgrade\Contracts\MigratesStructure;
use LaravelEnso\Upgrade\Contracts\ShouldRunInConsole;
use LaravelEnso\Upgrade\Contracts\Upgrade as Contract;
use LaravelEnso\Upgrade\Exceptions\MissingInterface;

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

    private function run($upgrade)
    {
        if (! $upgrade instanceof ShouldRunInConsole || App::runningInConsole()) {
            (new Database($this->upgrade($upgrade)))->handle();
        }
    }

    private function upgrade($upgrade)
    {
        if ($upgrade instanceof MigratesStructure) {
            return new Structure($upgrade);
        }

        if ($upgrade instanceof Contract) {
            return $upgrade;
        }

        throw new MissingInterface();
    }
}
