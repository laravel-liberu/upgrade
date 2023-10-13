<?php

namespace LaravelLiberu\Upgrade\Contracts;

interface RollbackTableMigration extends Upgrade
{
    public function rollbackTableMigration(): void;
}
