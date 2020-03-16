<?php

namespace LaravelEnso\Upgrade\App\Contracts;

interface RollbackTableMigration extends Upgrade
{
    public function rollbackTableMigration(): void;
}
