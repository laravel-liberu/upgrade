<?php

namespace LaravelLiberu\Upgrade\Contracts;

interface Prioritization
{
    public const Default = 100;

    public function priority(): int;
}
