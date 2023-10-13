<?php

namespace LaravelLiberu\Upgrade\Contracts;

interface MigratesStructure
{
    public function permissions(): array;

    public function roles(): array;
}
