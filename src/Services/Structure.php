<?php

namespace LaravelLiberu\Upgrade\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use LaravelLiberu\Permissions\Models\Permission;
use LaravelLiberu\Roles\Models\Role;
use LaravelLiberu\Upgrade\Contracts\Applicable;
use LaravelLiberu\Upgrade\Contracts\MigratesData;
use LaravelLiberu\Upgrade\Contracts\MigratesPostDataMigration;
use LaravelLiberu\Upgrade\Contracts\MigratesStructure;
use LaravelLiberu\Upgrade\Contracts\Prioritization;
use LaravelLiberu\Upgrade\Contracts\Upgrade;
use ReflectionClass;

class Structure implements Upgrade, MigratesData, Prioritization, MigratesPostDataMigration, Applicable
{
    private Collection $existing;
    private Collection $roles;
    private readonly string $defaultRole;

    public function __construct(private readonly MigratesStructure $upgrade)
    {
        $this->defaultRole = Config::get('liberu.config.defaultRole');
    }

    public function isMigrated(): bool
    {
        $permissions = Collection::wrap($this->upgrade->permissions())->pluck('name');

        $this->existing = Permission::whereIn('name', $permissions)->pluck('name');

        return $this->existing->count() >= $permissions->count();
    }

    public function migrateData(): void
    {
        Collection::wrap($this->upgrade->permissions())
            ->reject(fn ($permission) => $this->existing->contains($permission['name']))
            ->each(fn ($permission) => $this->storeWithRoles($permission));

        if (App::isLocal()) {
            $this->roles()
                ->reject(fn ($role) => $role->name === $this->defaultRole)
                ->each->writeConfig();
        }
    }

    public function reflection()
    {
        return new ReflectionClass($this->upgrade);
    }

    public function priority(): int
    {
        return $this->upgrade instanceof Prioritization
            ? $this->upgrade->priority()
            : Prioritization::Default;
    }

    public function migratePostDataMigration(): void
    {
        if ($this->upgrade instanceof MigratesPostDataMigration) {
            $this->upgrade->migratePostDataMigration();
        }
    }

    public function applicable(): bool
    {
        return ! $this->upgrade instanceof Applicable
            || $this->upgrade->applicable();
    }

    private function storeWithRoles(array $permission): void
    {
        $permission = Permission::create($permission);

        $permission->roles()
            ->sync($this->syncRoles($permission));
    }

    private function syncRoles(Permission $permission): Collection
    {
        return $this->roles()->when(! $permission->is_default, fn ($roles) => $roles
            ->filter(fn ($role) => in_array($role->name, $this->upgrade->roles())
                || $role->name === $this->defaultRole));
    }

    private function roles(): Collection
    {
        return $this->roles ??= Role::get();
    }
}
