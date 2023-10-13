<?php

namespace LaravelLiberu\Upgrade\Contracts;

interface MigratesTable extends Upgrade
{
    public function migrateTable(): void;
}
