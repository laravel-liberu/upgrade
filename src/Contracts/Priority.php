<?php

namespace LaravelEnso\Upgrade\Contracts;

interface Priority
{
    public const Default = 100;

    /**
     * default priority is 100
     * check order of upgrade with enso:upgrade:status
     *
     */
    public function priority(): int;
}