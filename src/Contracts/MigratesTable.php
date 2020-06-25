<?php

namespace LaravelEnso\Upgrade\Contracts;

interface MigratesTable extends Upgrade
{
    public function migrateTable(): void;
}
