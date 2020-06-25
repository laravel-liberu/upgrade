<?php

namespace LaravelEnso\Upgrade\Contracts;

interface RollbackTableMigration extends Upgrade
{
    public function rollbackTableMigration(): void;
}
