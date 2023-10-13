<?php

namespace LaravelLiberu\Upgrade\Traits;

use Doctrine\DBAL\Types\Type;
use LaravelLiberu\Upgrade\Helpers\DBAL\TinyInteger;

trait UpgradesMySqlTinyInteger
{
    public function __construct()
    {
        if (!array_key_exists(TinyInteger::NAME, Type::getTypesMap())) {
            Type::addType(TinyInteger::NAME, TinyInteger::class);
        }
    }
}
