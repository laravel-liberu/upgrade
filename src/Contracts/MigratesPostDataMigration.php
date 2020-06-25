<?php

namespace LaravelEnso\Upgrade\Contracts;

interface MigratesPostDataMigration extends Upgrade
{
    public function migratePostDataMigration(): void;
}
