<?php

namespace LaravelEnso\Upgrade\Helpers\DBAL;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\PhpIntegerMappingType;
use Doctrine\DBAL\Types\Type;

class TinyInteger extends Type implements PhpIntegerMappingType
{
    final public const NAME = 'tinyinteger';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return "TINYINT {$this->getUnsignedDeclaration($column)}";
    }

    public function getName()
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : (int) $value;
    }

    public function getBindingType()
    {
        return ParameterType::INTEGER;
    }

    private function getUnsignedDeclaration(array $column): string
    {
        return empty($column['unsigned']) ? '' : ' UNSIGNED';
    }
}
