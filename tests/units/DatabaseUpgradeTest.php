<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use LaravelEnso\Upgrade\Contracts\MigratesData;
use LaravelEnso\Upgrade\Contracts\MigratesPostDataMigration;
use LaravelEnso\Upgrade\Contracts\MigratesTable;
use LaravelEnso\Upgrade\Contracts\RollbackTableMigration;
use LaravelEnso\Upgrade\Helpers\Table;
use LaravelEnso\Upgrade\Services\Upgrade as Service;
use Tests\TestCase;

class DatabaseUpgradeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_upgrade()
    {
        (new Service([TestDatabaseMigration::class]))->handle();

        $this->assertTrue(Table::hasColumn('test', 'name'));
        $this->assertTrue(Table::hasColumn('test', 'post_migration'));
        $this->assertNotEmpty(DB::table('test')->get());
    }

    /** @test */
    public function cannot_migrate_when_data_migration_fails()
    {
        $this->expectException(Exception::class);

        (new Service([FailingDataMigrationTest::class]))->handle();

        $this->assertFalse(Schema::hasTable('test'));
    }

    /** @test */
    public function cannot_migrate_when_post_data_migration_fails()
    {
        $this->expectException(Exception::class);

        (new Service([FailingPostMigrationTest::class]))->handle();

        $this->assertFalse(Schema::hasTable('test'));
    }

    /** @test */
    public function cannot_migrate_twice()
    {
        (new Service([AlreadyMigratedMigrationTest::class]))->handle();

        $this->assertFalse(Schema::hasTable('test'));
    }
}

class TestDatabaseMigration implements MigratesTable, MigratesData, MigratesPostDataMigration, RollbackTableMigration
{
    public function isMigrated(): bool
    {
        return Schema::hasTable('test')
            && DB::table('test')->whereName('model')->exists();
    }

    public function migrateTable(): void
    {
        Schema::create('test', function ($table) {
            $table->string('name');
        });
    }

    public function migrateData(): void
    {
        DB::insert('INSERT INTO test VALUES("model")');
    }

    public function rollbackTableMigration(): void
    {
        Schema::dropIfExists('test');
    }

    public function migratePostDataMigration(): void
    {
        Schema::table('test', function ($table) {
            $table->string('post_migration')->nullable();
        });
    }
}

class FailingDataMigrationTest extends TestDatabaseMigration
{
    public function migrateData(): void
    {
        parent::migrateData();

        throw new Exception();
    }
}

class FailingPostMigrationTest extends TestDatabaseMigration
{
    public function migratePostDataMigration(): void
    {
        parent::migratePostDataMigration();

        throw new Exception();
    }
}

class AlreadyMigratedMigrationTest extends TestDatabaseMigration
{
    public function isMigrated(): bool
    {
        return true;
    }
}
