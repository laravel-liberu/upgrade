<?php

namespace LaravelEnso\Upgrade;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\Upgrade\Commands\Upgrade;
use LaravelEnso\Upgrade\Commands\UpgradeStatus;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([Upgrade::class, UpgradeStatus::class]);
    }
}
