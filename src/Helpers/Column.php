<?php

namespace LaravelLiberu\Upgrade\Helpers;

use Doctrine\DBAL\Schema\Column as DoctrineColumn;
use Illuminate\Support\Facades\Schema;

class Column
{
    public static function isNullable(string $table, string $column): bool
    {
        return ! self::isNotNullable($table, $column);
    }

    public static function isSigned(string $table, string $column): bool
    {
        return ! self::isUnsigned($table, $column);
    }

    public static function isUnsigned(string $table, string $column): bool
    {
        return self::doctrineColumn($table, $column)->getUnsigned();
    }

    public static function isNotNullable(string $table, string $column): bool
    {
        return self::doctrineColumn($table, $column)->getNotnull();
    }

    public static function isDecimal(string $table, string $column): bool
    {
        return self::isType($table, $column, 'decimal');
    }

    public static function isInteger(string $table, string $column): bool
    {
        return self::isType($table, $column, 'integer');
    }

    public static function isBigInteger(string $table, string $column): bool
    {
        return self::isType($table, $column, 'bigint');
    }

    public static function isTinyInteger(string $table, string $column): bool
    {
        return self::isBoolean($table, $column);
    }

    public static function isBoolean(string $table, string $column): bool
    {
        return self::isType($table, $column, 'boolean');
    }

    public static function isText(string $table, string $column): bool
    {
        return self::isType($table, $column, 'boolean');
    }

    public static function isDate(string $table, string $column): bool
    {
        return self::isType($table, $column, 'date');
    }

    public static function isDateTime(string $table, string $column): bool
    {
        return self::isType($table, $column, 'datetime');
    }

    public static function isString(string $table, string $column): bool
    {
        return self::isType($table, $column, 'string');
    }

    public static function getPrecision(string $table, string $column): int
    {
        return self::doctrineColumn($table, $column)->getPrecision();
    }

    public static function getScale(string $table, string $column): int
    {
        return self::doctrineColumn($table, $column)->getScale();
    }

    public static function getLength(string $table, string $column): int
    {
        return self::doctrineColumn($table, $column)->getLength();
    }

    private static function isType(string $table, string $column, string $type): bool
    {
        return Schema::getColumnType($table, $column) === $type;
    }

    private static function doctrineColumn(string $table, string $column): DoctrineColumn
    {
        return Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableDetails($table)
            ->getColumn($column);
    }
}
