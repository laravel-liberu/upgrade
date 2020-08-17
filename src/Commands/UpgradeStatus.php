<?php

namespace LaravelEnso\Upgrade\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use LaravelEnso\Upgrade\Services\UpgradeStatus as Service;

class UpgradeStatus extends Command
{
    protected $signature = 'enso:upgrade:status';

    protected $description = 'This command will print upgrades status';

    public function handle()
    {
        $this->table(
            ['Ran?', 'Upgrade', 'priority', 'Changed At'],
            $this->rows()
        );
    }

    private function rows(): Collection
    {
        return (new Service())
            ->handle()
            ->map(fn ($status) => array_merge($status, [
                'isMigrated' => $status['isMigrated'] ? $this->green('Yes') : $this->red('No'),
                'changedAt' => __(':dateTime (:diff)', [
                    'dateTime' => $status['changedAt']->format('Y-m-d H:i:s'),
                    'diff' => $status['changedAt']->diffForHumans(),
                ])
            ]));
    }

    private function green($msg): string
    {
        return "<info>$msg</info>";
    }

    private function red($msg): string
    {
        return "<fg=red>$msg</fg=red>";
    }
}
