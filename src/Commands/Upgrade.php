<?php

namespace LaravelEnso\Upgrade\Commands;

use Illuminate\Console\Command;
use LaravelEnso\Upgrade\Services\Upgrade as Service;

class Upgrade extends Command
{
    protected $signature = 'enso:upgrade {--manual}';

    protected $description = 'This command will upgrade Enso to the latest version';

    public function handle()
    {
        (new Service())
            ->manual($this->option('manual'))
            ->handle();
    }
}
