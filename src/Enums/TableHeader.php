<?php

namespace LaravelEnso\Upgrade\Enums;

use LaravelEnso\Enums\Services\Enum;

class TableHeader extends Enum
{
    public const NrCrt = 'Nr Crt';
    public const Package = 'Package';
    public const Upgrade = 'Upgrade';
    public const Applicable = 'Applicable';
    public const Manual = 'Manual';
    public const Priority = 'Priority';
    public const Ran = 'Ran';
    public const ChangedAt = 'Changed At';
}
