<?php

namespace LaravelEnso\Upgrade\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use LaravelEnso\Upgrade\Contracts\Prioritization;
use LaravelEnso\Upgrade\Contracts\ShouldRunManually;
use LaravelEnso\Upgrade\Contracts\Upgrade as Contract;
use ReflectionClass;

class Upgrade
{
    protected $finder;

    public function __construct($finder = null)
    {
        $this->finder = $finder ?? new Finder();
    }

    public function handle()
    {
        $this->sorted()
            ->filter(fn ($upgrade) => $this->canRun($upgrade))
            ->each(fn ($upgrade) => (new Database($upgrade))->handle());
    }

    protected function sorted(): Collection
    {
        return $this->finder->upgrades()
            ->sortBy(fn ($upgrade) => $this->priority($upgrade).$this->changedAt($upgrade)->timestamp);
    }

    protected function priority(Contract $upgrade): int
    {
        return $upgrade instanceof Prioritization
            ? $upgrade->priority()
            : Prioritization::Default;
    }

    protected function reflection(Contract $upgrade): ReflectionClass
    {
        return $upgrade instanceof Structure
            ? $upgrade->reflection()
            : new ReflectionClass($upgrade);
    }

    protected function changedAt($upgrade): Carbon
    {
        return Carbon::createFromTimestamp(
            File::lastModified($this->reflection($upgrade)->getFileName())
        );
    }

    private function canRun($upgrade): bool
    {
        return ! $upgrade instanceof ShouldRunManually
            || App::runningInConsole();
    }
}
