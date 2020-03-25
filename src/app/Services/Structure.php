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

    public function __construct(MigratesStructure $upgrade)
    {
        $this->upgrade = $upgrade;
    }

    public function isMigrated(): bool
    {
        $permissions = $this->upgrade->permissions()->pluck('name');

        $this->existing = Permission::whereIn('name', $permissions)
            ->get()->pluck('name');

        return $permissions->isEmpty()
            || $this->existing->count() === $permissions->count();
    }

    public function migrateData(): void
    {
        $this->upgrade->permissions()
            ->filter(fn ($permission) => ! $this->existing->contains($permission['name']))
            ->each(fn ($permission) => $this->permission($permission));

        if (App::isLocal()) {
            $this->upgradeRoles
                ->filter(fn ($role) => $role->name !== Config::get('enso.config.defaultRole'))
                ->each->writeConfig();
        }
    }

    private function permission(array $permission): void
    {
        $permission = (Permission::create($permission));

        if (! App::isProduction()) {
            $this->syncRoles($permission);
        }
    }

    private function syncRoles(Permission $permission)
    {
        $roles = $this->roles($permission);

        $permission->roles()->sync($roles->pluck('id'));
    }

    private function roles(Permission $permission): Collection
    {
        return $permission->is_default
            ? $this->allRoles()
            : $this->upgradeRoles();
    }

    private function allRoles()
    {
        return $this->allRoles ??= Role::get();
    }

    private function upgradeRoles()
    {
        $defaultRole = Config::get('enso.config.defaultRole');

        if (! isset($this->upgradeRoles)) {
            $roles = $this->upgrade->roles()->isNotEmpty()
                ? $this->upgrade->roles()->push($defaultRole)->unique()
                : [$defaultRole];

            $this->upgradeRoles = Role::whereIn('name', $roles)->get();
        }

        return $this->upgradeRoles;
    }
}
