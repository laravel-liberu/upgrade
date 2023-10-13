<?php

namespace LaravelLiberu\Upgrade\Enums;

use LaravelLiberu\Enums\Services\Enum;

class TableHeader extends Enum
{
    final public const NrCrt = 1;
    final public const Package = 2;
    final public const Upgrade = 3;
    final public const Applicable = 4;
    final public const Manual = 5;
    final public const Priority = 6;
    final public const Migration = 7;
    final public const Ran = 8;
    final public const LastModifiedAt = 9;

    protected static array $data = [
        self::NrCrt => 'Nr Crt',
        self::Package => 'Package',
        self::Upgrade => 'Upgrade',
        self::Applicable => 'Applicable',
        self::Manual => 'Manual',
        self::Priority => 'Priority',
        self::Migration => 'Migration',
        self::Ran => 'Ran',
        self::LastModifiedAt => 'Last Modified At',
    ];
}
