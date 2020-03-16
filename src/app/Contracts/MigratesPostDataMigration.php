<?php

namespace LaravelEnso\Upgrade\App\Contracts;

interface MigratesPostDataMigration extends Upgrade
{
    public function migratePostDataMigration(): void;
}
