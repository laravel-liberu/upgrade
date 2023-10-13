<?php

namespace LaravelLiberu\Upgrade;

use Illuminate\Support\ServiceProvider;
use LaravelLiberu\Upgrade\Commands\Upgrade;
use LaravelLiberu\Upgrade\Commands\UpgradeStatus;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->load()
            ->publish()
            ->commands(Upgrade::class, UpgradeStatus::class);
    }

    private function load(): self
    {
        $this->mergeConfigFrom(__DIR__.'/../config/upgrade.php', 'enso.upgrade');

        return $this;
    }

    private function publish(): self
    {
        $this->publishes([
            __DIR__.'/../config' => config_path('enso'),
        ], ['upgrade-config', 'enso-config']);

        return $this;
    }
}
