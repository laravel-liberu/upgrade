<?php

namespace LaravelLiberu\Upgrade\Contracts;

interface MigratesData extends Upgrade
{
    public function migrateData(): void;
}
