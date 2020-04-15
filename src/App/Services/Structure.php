<?php

namespace LaravelEnso\Upgrade\App\Services;

use Illuminate\Database\Eloquent\Collection;
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
    private string $defaultRole;

    public function __construct(MigratesStructure $upgrade)
    {
        $this->upgrade = $upgrade;
    }

    public function isMigrated(): bool
    {
        $permissions = $this->upgrade->permissions()->pluck('name');

        $this->existing = Permission::whereIn('name', $permissions)->pluck('name');

        return $this->existing->count() === $permissions->count();
    }

    public function migrateData(): void
    {
        $this->defaultRole = Config::get('enso.config.defaultRole');

        $this->upgrade->permissions()
            ->reject(fn ($permission) => $this->existing->contains($permission['name']))
            ->each(fn ($permission) => $this->create($permission));

        if (App::isLocal()) {
            $this->allRoles()
                ->reject(fn ($role) => $role->name === $this->defaultRole)
                ->each->writeConfig();
        }
    }

    private function create(array $permission): Permission
    {
        $permission = Permission::create($permission);

        if (App::isLocal()) {
            $permission->roles()->sync($this->roles($permission));
        }

        return $permission;
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
        return $this->upgradeRoles ??= Role::query()
            ->whereIn('name', [$this->defaultRole, ...$this->upgrade->roles()])
            ->get();
    }
}
