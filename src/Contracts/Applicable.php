<?php

namespace LaravelEnso\Upgrade\Contracts;

interface Applicable
{
    public function applicable(): bool;
}
