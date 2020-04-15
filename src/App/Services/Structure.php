<?php

namespace LaravelEnso\Upgrade\App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use LaravelEnso\Permissions\App\Models\Permission;
use LaravelEnso\Roles\App\Models\Role;
use LaravelEnso\Upgrade\App\Contracts\MigratesData;
use LaravelEnso\Upgrade\App\Contracts\MigratesStructure;
use LaravelEnso\Upgrade\App\Contracts\Upgrade;

class Structure implements Upgrade, MigratesData
{
    private MigratesStructure $upgrade;
    private Collection $existing;
    private Collection $allRoles;
    private Collection $upgradeRoles;
    private Role $defaultRole;

    public function __construct(MigratesStructure $upgrade)
    {
        $this->upgrade = $upgrade;
    }

    public function isMigrated(): bool
    {
        $permissions = $this->upgrade->permissions()->pluck('name');

        $this->existing = Permission::whereIn('name', $permissions)->pluck('name');

        return $this->existing->count() >= $permissions->count();
    }

    public function migrateData(): void
    {
        $this->defaultRole = Role::whereName(Config::get('enso.config.defaultRole'))->first();

        $this->upgrade->permissions()
            ->reject(fn ($permission) => $this->existing->contains($permission['name']))
            ->each(fn ($permission) => $this->storeWithRoles($permission));

        if (App::isLocal()) {
            $this->allRoles()
                ->reject(fn ($role) => $role->is($this->defaultRole))
                ->each->writeConfig();
        }
    }

    private function storeWithRoles(array $permission): void
    {
        $permission = Permission::create($permission);

        if (App::isLocal()) {
            $permission->roles()
                ->sync($this->roles($permission));
        } else {
            $permission->roles()->attach($this->defaultRole);
        }
    }

    private function roles(Permission $permission): Collection
    {
        return $permission->is_default
            ? $this->allRoles()
            : $this->upgradeRoles();
    }

    private function allRoles(): Collection
    {
        return $this->allRoles ??= Role::get();
    }

    private function upgradeRoles()
    {
        $hasAdmin = $this->upgrade->roles()
            ->some(fn ($role) => $role === $this->defaultRole->name);

        return $this->upgradeRoles ??= Role::query()
            ->whereIn('name', $this->upgrade->roles())
            ->get()
            ->when(! $hasAdmin, fn ($roles) => $roles->push($this->defaultRole));
    }
}
