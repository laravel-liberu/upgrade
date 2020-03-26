<?php

namespace LaravelEnso\Upgrade\App\Contracts;

interface MigratesTable extends Upgrade
{
    public function migrateTable(): void;
}
