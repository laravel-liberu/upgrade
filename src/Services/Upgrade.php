<?php

namespace LaravelEnso\Upgrade\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use LaravelEnso\Upgrade\Contracts\Priority;
use LaravelEnso\Upgrade\Contracts\ShouldRunInConsole;
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
        return $upgrade instanceof Priority
            ? $upgrade->priority()
            : Priority::Default;
    }

    protected function reflection(Contract $upgrade): ReflectionClass
    {
        return $upgrade instanceof Structure
            ? $upgrade->reflection()
            : new ReflectionClass($upgrade);
    }

    protected function changedAt($upgrade): Carbon
    {
        return Carbon::createFromTimestamp(filectime($this->reflection($upgrade)->getFileName()));
    }

    private function canRun($upgrade): bool
    {
        return ! $upgrade instanceof ShouldRunInConsole
            || App::runningInConsole();
    }
}
