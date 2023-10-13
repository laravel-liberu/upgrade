<?php

namespace LaravelLiberu\Upgrade\Services;

use Illuminate\Support\Collection;
use LaravelLiberu\Roles\Models\Role;
use LaravelLiberu\Upgrade\Contracts\Applicable;
use LaravelLiberu\Upgrade\Contracts\BeforeMigration;
use LaravelLiberu\Upgrade\Contracts\Prioritization;
use LaravelLiberu\Upgrade\Contracts\ShouldRunManually;
use LaravelLiberu\Upgrade\Contracts\Upgrade as Contract;

class Upgrade
{
    protected $finder;
    private bool $beforeMigration;
    private bool $manual;

    public function __construct($finder = null)
    {
        $this->finder = $finder ?? new Finder();
        $this->manual = false;
        $this->beforeMigration = false;
    }

    public function manual(bool $manual): self
    {
        $this->manual = $manual;

        return $this;
    }

    public function beforeMigration(bool $beforeMigration): self
    {
        $this->beforeMigration = $beforeMigration;

        return $this;
    }

    public function handle()
    {
        $this->sorted()
            ->filter(fn ($upgrade) => $this->canRun($upgrade))
            ->map(fn ($upgrade) => new Database($upgrade))
            ->each->handle();

        Role::all()->each->clearPermissionCache();
    }

    protected function sorted(): Collection
    {
        $timestamp = fn ($a) => Reflection::lastModifiedAt($a)->timestamp;

        return $this->finder->upgrades()->sortBy([
            fn ($a, $b) => $this->priority($a) - $this->priority($b),
            fn ($a, $b) => $timestamp($b) - $timestamp($a),
        ]);
    }

    protected function priority(Contract $upgrade): int
    {
        return $upgrade instanceof Prioritization
            ? $upgrade->priority()
            : Prioritization::Default;
    }

    private function canRun($upgrade): bool
    {
        if ($upgrade instanceof BeforeMigration ^ $this->beforeMigration) {
            return false;
        }

        if ($upgrade instanceof ShouldRunManually && ! $this->manual) {
            return false;
        }

        if ($upgrade instanceof Applicable && ! $upgrade->applicable()) {
            return false;
        }

        return true;
    }
}
