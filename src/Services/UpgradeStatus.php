<?php

namespace LaravelLiberu\Upgrade\Services;

use Illuminate\Support\Facades\Config;
use LaravelLiberu\Upgrade\Contracts\Applicable;
use LaravelLiberu\Upgrade\Contracts\BeforeMigration;
use LaravelLiberu\Upgrade\Contracts\ShouldRunManually;
use LaravelLiberu\Upgrade\Contracts\Upgrade as Contract;
use LaravelLiberu\Upgrade\Enums\TableHeader;

class UpgradeStatus extends Upgrade
{
    public function handle()
    {
        return $this->sorted()->values()->map(fn (Contract $upgrade, $index) => [
            TableHeader::NrCrt => $index + 1,
            TableHeader::Package => Reflection::package($upgrade),
            TableHeader::Upgrade => Reflection::upgrade($upgrade),
            TableHeader::Applicable => $this->applicable($upgrade),
            TableHeader::Manual => $this->isManual($upgrade),
            TableHeader::Priority => $this->priority($upgrade),
            TableHeader::Migration => $this->migration($upgrade),
            TableHeader::Ran => $this->ran($upgrade),
            TableHeader::LastModifiedAt => $this->changedAt($upgrade),
        ]);
    }

    private function applicable(Contract $upgrade): string
    {
        return ! $upgrade instanceof Applicable || $upgrade->applicable()
            ? $this->green('yes')
            : $this->yellow('no');
    }

    private function isManual(Contract $upgrade): string
    {
        return $upgrade instanceof ShouldRunManually
            ? $this->yellow('yes')
            : $this->green('no');
    }

    private function changedAt(Contract $upgrade): string
    {
        $lastModifiedAt = Reflection::lastModifiedAt($upgrade);
        $format = Config::get('enso.config.dateTimeFormat');

        return "{$lastModifiedAt->format($format)} ({$lastModifiedAt->diffForHumans()})";
    }

    private function migration(Contract $upgrade): string
    {
        return $upgrade instanceof BeforeMigration
            ? $this->yellow('before')
            : $this->green('after');
    }

    private function ran(Contract $upgrade): string
    {
        return $upgrade->isMigrated()
            ? $this->green('yes')
            : $this->red('no');
    }

    private function green($label): string
    {
        return "<info>{$label}</info>";
    }

    private function red($label): string
    {
        return "<fg=red>{$label}</fg=red>";
    }

    private function yellow($label): string
    {
        return "<fg=yellow>{$label}</fg=yellow>";
    }
}
