<?php

namespace LaravelLiberu\Upgrade\Helpers;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Table
{
    public static function exists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    public static function hasIndex(string $table, string $index): bool
    {
        $currentIndexes = Schema::getConnection()->getDoctrineSchemaManager()
            ->listTableIndexes($table);

        return Collection::wrap($currentIndexes)->has($index);
    }

    public static function hasColumn(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }

    public static function hasForeignKey(string $table, string $name): bool
    {
        return Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableDetails($table)
            ->hasForeignKey($name);
    }

    public static function foreignKey(string $table, string $name): ?ForeignKeyConstraint
    {
        return Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableDetails($table)
            ->getForeignKey($name);
    }

    public static function hasType(string $table, string $column, string $type): bool
    {
        $field = Collection::wrap(DB::select("SHOW FIELDS FROM {$table}"))
            ->first(fn ($col) => $col->Field === $column);

        return $field
            ? $field->Type === $type
            : false;
    }
}
