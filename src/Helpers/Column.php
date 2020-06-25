<?php

namespace LaravelEnso\Upgrade\Helpers;

use Illuminate\Support\Facades\Schema;

class Column
{
    public static function isNotNullable(string $table, string $column): bool
    {
        return Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableDetails($table)
            ->getColumn($column)
            ->getNotnull();
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

    private static function isType(string $table, string $column, string $type): bool
    {
        return Schema::getColumnType($table, $column) === $type;
    }
}
