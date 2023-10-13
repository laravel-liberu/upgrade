<?php

namespace LaravelLiberu\Upgrade\Commands;

use Illuminate\Console\Command;
use LaravelLiberu\Upgrade\Services\Upgrade as Service;

class Upgrade extends Command
{
    protected $signature = 'liberu:upgrade {--manual} {--before-migration}';

    protected $description = 'This command will upgrade your Enso project to the latest version';

    public function handle()
    {
        (new Service())
            ->manual($this->option('manual'))
            ->beforeMigration($this->option('before-migration'))
            ->handle();
    }
}
