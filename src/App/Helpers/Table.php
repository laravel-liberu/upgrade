<?php

namespace LaravelEnso\Upgrade\App\Helpers;

use Illuminate\Support\Facades\Schema;

class Table
{
    public static function hasIndex(string $table, string $index): bool
    {
        return array_key_exists($index, Schema::getConnection()
            ->getDoctrineSchemaManager()->listTableIndexes($table));
    }

    public static function hasColumn(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }
}
