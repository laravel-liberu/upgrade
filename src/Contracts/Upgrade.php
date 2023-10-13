<?php

namespace LaravelLiberu\Upgrade\Contracts;

interface Upgrade
{
    public function isMigrated(): bool;
}
