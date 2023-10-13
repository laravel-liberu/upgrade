<?php

namespace LaravelLiberu\Upgrade\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use LaravelLiberu\Upgrade\Contracts\MigratesStructure;

class Finder
{
    public function upgrades(): Collection
    {
        return $this->upgradePackages()
            ->map(fn ($folder) => $this->upgradeClasses($folder))
            ->flatten();
    }

    private function upgradePackages(): Collection
    {
        return Collection::wrap(Config::get('liberu.upgrade.vendors'))
            ->map(fn ($vendor) => base_path("vendor/{$vendor}"))
            ->map(fn ($vendor) => File::directories($vendor))
            ->flatten()
            ->concat(Collection::wrap(Config::get('liberu.upgrade.folders'))
                ->map(fn ($folder) => base_path($folder)))
            ->map(fn ($path) => new Package($path))
            ->filter->qualifies();
    }

    private function upgradeClasses(Package $package): Collection
    {
        return $package->upgradeClasses()
            ->map(fn ($class) => new $class)
            ->map(fn ($upgrade) => $upgrade instanceof MigratesStructure
                ? new Structure($upgrade)
                : new $upgrade);
    }
}
