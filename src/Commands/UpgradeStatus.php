<?php

namespace LaravelLiberu\Upgrade\Commands;

use Illuminate\Console\Command;
use LaravelLiberu\Upgrade\Enums\TableHeader;
use LaravelLiberu\Upgrade\Services\UpgradeStatus as Service;

class UpgradeStatus extends Command
{
    protected $signature = 'liberu:upgrade:status';

    protected $description = "This command will display the existing upgrade's status";

    public function handle()
    {
        $this->table(TableHeader::values()->toArray(), (new Service())->handle());
    }
}
