<?php

namespace LaravelEnso\Upgrade\Traits;

use Doctrine\DBAL\Types\Type;
use LaravelEnso\Upgrade\Helpers\Doctrine\TinyInteger;

trait UpgradesMySqlTinyInteger
{
    public function __construct()
    {
        if (!array_key_exists(TinyInteger::NAME, Type::getTypesMap())) {
            Type::addType(TinyInteger::NAME, TinyInteger::class);
        }
    }
}
