<?php

namespace LaravelEnso\Upgrade\App\Contracts;

interface MigratesData extends Upgrade
{
    public function migrateData(): void;
}
