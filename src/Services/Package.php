<?php

namespace LaravelEnso\Upgrade\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use LaravelEnso\Helpers\Services\JsonReader;
use LaravelEnso\Upgrade\Contracts\MigratesStructure;
use LaravelEnso\Upgrade\Contracts\Upgrade;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class Package
{
    private string $folder;
    private array $composer;

    public function __construct(string $folder)
    {
        $this->folder = $folder;
    }

    public function qualifies(): bool
    {
        return File::exists($this->folder.DIRECTORY_SEPARATOR.'composer.json')
            && $this->hasUpgrades();
    }

    public function upgradeClasses()
    {
        return (new Collection(File::allFiles($this->upgradeFolder())))
            ->map(fn (SplFileInfo $file) => $this->namespace(
                'Upgrades',
                $file->getRelativePath('Upgrades'),
                $file->getFilenameWithoutExtension()
            ))->filter(fn ($class) => $this->isUpgrade($class));
    }

    private function hasUpgrades(): bool
    {
        return File::isDirectory($this->upgradeFolder());
    }

    private function namespace(...$segments): string
    {
        return (new Collection([
            rtrim($this->psr4Namespace(), '\\'), ...$segments,
        ]))->filter()->implode('\\');
    }

    private function upgradeFolder()
    {
        return $this->appFolder('Upgrades');
    }

    private function appFolder(...$segments): string
    {
        return (new Collection([$this->folder, $this->psr4Folder(), ...$segments]))
            ->implode(DIRECTORY_SEPARATOR);
    }

    private function psr4Folder()
    {
        return $this->composer()['autoload']['psr-4'][$this->psr4Namespace()];
    }

    private function psr4Namespace()
    {
        return key($this->composer()['autoload']['psr-4']);
    }

    private function composer(): array
    {
        return isset($this->composer)
            ? $this->composer
            : $this->composer = (new JsonReader(
                $this->folder.DIRECTORY_SEPARATOR.'composer.json'
            ))->array();
    }

    private function isUpgrade($class): bool
    {
        $reflection = new ReflectionClass($class);

        return $reflection->implementsInterface(MigratesStructure::class)
            || $reflection->implementsInterface(Upgrade::class);
    }
}
