<?php

namespace LaravelEnso\Upgrade\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class Package
{
    private string $dir;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    public function isPackage(): bool
    {
        return File::exists($this->dir.DIRECTORY_SEPARATOR.'composer.json');
    }

    public function hasUpgrade(): bool
    {
        return is_dir($this->upgradeDir());
    }

    public function upgradeClasses(...$parts)
    {
        return (new Collection(File::allFiles($this->upgradeDir())))
            ->map(fn (SplFileInfo $file) => $this->namespace(
                'Upgrades',
                $file->getRelativePath('Upgrades'),
                $file->getFilenameWithoutExtension()
            ));
    }

    private function appDir(...$parts): string
    {
        return (new Collection([
            $this->dir, $this->psr4Dir(), ...$parts,
        ]))->implode(DIRECTORY_SEPARATOR);
    }

    private function namespace(...$parts): string
    {
        return (new Collection([
            rtrim($this->psr4Namespace(), '\\'), ...$parts
        ]))->filter()->implode('\\');
    }

    private function upgradeDir(...$parts)
    {
        return $this->appDir('Upgrades', ...$parts);
    }

    private function psr4Dir()
    {
        return $this->composer()['autoload']['psr-4'][$this->psr4Namespace()];
    }

    private function psr4Namespace()
    {
        return key($this->composer()['autoload']['psr-4']);
    }

    private function composer(): array
    {
        return json_decode(
            File::get($this->dir.DIRECTORY_SEPARATOR.'composer.json'),
            true
        );
    }
}
