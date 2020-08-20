<?php

namespace LaravelEnso\Upgrade\Services;

use LaravelEnso\Upgrade\Contracts\Upgrade as Contract;

class UpgradeStatus extends Upgrade
{
    public function handle()
    {
        return $this->sorted()->map(fn (Contract $upgrade) => [
            'isMigrated' => $upgrade->isMigrated(),
            'namespace' => $this->reflection($upgrade)->getName(),
            'priority' => $this->priority($upgrade),
            'changedAt' => $this->changedAt($upgrade),
        ]);
    }
}
