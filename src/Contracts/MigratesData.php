<?php

namespace LaravelEnso\Upgrade\Contracts;

interface MigratesData extends Upgrade
{
    public function migrateData(): void;
}
