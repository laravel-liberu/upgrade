<?php

namespace LaravelEnso\Upgrade\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use LaravelEnso\Upgrade\Contracts\MigratesData;
use LaravelEnso\Upgrade\Contracts\MigratesPostDataMigration;
use LaravelEnso\Upgrade\Contracts\MigratesTable;
use LaravelEnso\Upgrade\Contracts\RollbackTableMigration;
use LaravelEnso\Upgrade\Contracts\Upgrade;
use ReflectionClass;
use Symfony\Component\Console\Output\ConsoleOutput;
use Throwable;

class Database extends Command
{
    protected $output;
    private readonly ReflectionClass $reflection;
    private readonly string $title;
    private string $time;

    public function __construct(private readonly Upgrade $upgrade)
    {
        parent::__construct();
        $this->reflection = (new ReflectionClass($upgrade));
        $this->output = new ConsoleOutput();
        $this->title = $this->title();
    }

    public function handle()
    {
        if ($this->upgrade->isMigrated()) {
            $this->line("{$this->title} -> has been already done");
        } else {
            $this->start()->migrate()->end();
        }
    }

    public function line($string, $style = null, $verbosity = null)
    {
        if (! App::runningUnitTests()) {
            parent::line(...func_get_args());
        }
    }

    private function start()
    {
        $this->time = microtime(true);

        $this->warn("{$this->title} -> is starting");

        return $this;
    }

    private function migrate()
    {
        if ($this->migratesTable()) {
            $this->upgrade->migrateTable();
        }

        try {
            if ($this->migratesData()) {
                DB::transaction(fn () => $this->upgrade->migrateData());
            }

            if ($this->migratesPostDataMigration()) {
                $this->upgrade->migratePostDataMigration();
            }
        } catch (Throwable $throwable) {
            if ($this->rollbacksTableMigration()) {
                $this->upgrade->rollbackTableMigration();
            }

            $this->error("{$this->title} -> failed, doing rollback ({$this->duration()} ms)");

            throw $throwable;
        }

        return $this;
    }

    private function end()
    {
        $this->info("{$this->title} -> was successfully done ({$this->duration()} ms)");
    }

    private function migratesTable(): bool
    {
        return $this->reflection->implementsInterface(MigratesTable::class);
    }

    private function migratesData(): bool
    {
        return $this->reflection->implementsInterface(MigratesData::class);
    }

    private function migratesPostDataMigration(): bool
    {
        return $this->reflection->implementsInterface(MigratesPostDataMigration::class);
    }

    private function rollbacksTableMigration(): bool
    {
        return $this->reflection->implementsInterface(RollbackTableMigration::class);
    }

    private function duration(): int
    {
        return (int) ((microtime(true) - $this->time) * 1000);
    }

    private function title(): string
    {
        $package = Reflection::package($this->upgrade);
        $service = Reflection::upgrade($this->upgrade);

        return "{$package}/{$service}";
    }
}
