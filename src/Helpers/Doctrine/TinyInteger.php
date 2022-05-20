<?php

namespace LaravelEnso\Upgrade\Helpers\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TinyInteger extends Type
{
    const NAME = 'tinyinteger';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'TINYINT';
    }

    public function getName()
    {
        return self::NAME;
    }
}
