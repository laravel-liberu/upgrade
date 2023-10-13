<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use LaravelLiberu\Permissions\Models\Permission;
use LaravelLiberu\Roles\Models\Role;
use LaravelLiberu\Upgrade\Contracts\MigratesStructure;
use LaravelLiberu\Upgrade\Services\Database;
use LaravelLiberu\Upgrade\Services\Structure;
use LaravelLiberu\Upgrade\Traits\StructureMigration;
use Tests\TestCase;

class StructureUpgradeTest extends TestCase
{
    use RefreshDatabase;

    protected MigratesStructure $upgrade;
    protected $defaultRole;
    protected $secondaryRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->upgrade = new TestStructureMigration();

        $this->defaultRole = $this->role(Config::get('liberu.config.defaultRole'));

        $this->secondaryRole = $this->role('secondaryRole');
    }

    /** @test */
    public function can_migrate()
    {
        $this->upgrade->permissions = [
            ['name' => 'test', 'description' => 'test', 'is_default' => true],
        ];

        $this->migrateStructure();

        $this->assertTrue(Permission::whereName('test')->exists());
    }

    /** @test */
    public function can_migrate_default_permission()
    {
        $this->upgrade->permissions = [
            ['name' => 'test', 'description' => 'test', 'is_default' => true],
        ];

        $this->migrateStructure();

        $this->assertEquals('test', $this->defaultRole->permissions->first()->name);
        $this->assertEquals('test', $this->secondaryRole->permissions->first()->name);
    }

    /** @test */
    public function can_migrate_non_default_permission()
    {
        $this->upgrade->permissions = [
            ['name' => 'test', 'description' => 'test', 'is_default' => false],
        ];

        $this->migrateStructure();

        $this->assertEquals('test', $this->defaultRole->permissions->first()->name);
        $this->assertEmpty($this->secondaryRole->permissions);
    }

    /** @test */
    public function skips_existing_permissions()
    {
        $this->upgrade->permissions = [
            ['name' => 'test', 'description' => 'test', 'is_default' => true],
        ];

        $this->migrateStructure();
        $this->migrateStructure();

        $this->assertEquals(1, Permission::whereName('test')->count());
    }

    protected function role($name)
    {
        return Role::factory()->create([
            'name' => $name,
        ]);
    }

    private function migrateStructure()
    {
        (new Database(new Structure($this->upgrade)))->handle();
    }
}

class TestStructureMigration implements MigratesStructure
{
    use StructureMigration;

    public $permissions = [];
}
