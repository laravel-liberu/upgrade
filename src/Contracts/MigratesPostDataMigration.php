<?php

namespace LaravelLiberu\Upgrade\Contracts;

interface MigratesPostDataMigration extends Upgrade
{
    public function migratePostDataMigration(): void;
}
