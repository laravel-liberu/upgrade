<?php

namespace LaravelEnso\Upgrade\Helpers;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class Table
{
    public static function hasIndex(string $table, string $index): bool
    {
        $currentIndexes = Schema::getConnection()->getDoctrineSchemaManager()
            ->listTableIndexes($table);

        return (new Collection($currentIndexes))->has($index);
    }

    public static function hasColumn(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }

    public static function foreignKey(string $table, string $name): ?ForeignKeyConstraint
    {
        return Schema::getConnection()->getDoctrineSchemaManager()
            ->listTableDetails($table)
            ->getForeignKey($name);
    }
}
