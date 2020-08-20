<?php

namespace LaravelEnso\Upgrade\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class Package
{
    private string $folder;

    public function __construct(string $folder)
    {
        $this->folder = $folder;
    }

    public function isPackage(): bool
    {
        return File::exists($this->folder.DIRECTORY_SEPARATOR.'composer.json');
    }

    public function hasUpgrade(): bool
    {
        return File::isDirectory($this->upgradeFolder());
    }

    public function upgradeClasses()
    {
        return (new Collection(File::allFiles($this->upgradeFolder())))
            ->map(fn (SplFileInfo $file) => $this->namespace(
                'Upgrades',
                $file->getRelativePath('Upgrades'),
                $file->getFilenameWithoutExtension()
            ));
    }

    private function appFolder(...$parts): string
    {
        return (new Collection([
            $this->folder, $this->psr4Folder(), ...$parts,
        ]))->implode(DIRECTORY_SEPARATOR);
    }

    private function namespace(...$parts): string
    {
        return (new Collection([
            rtrim($this->psr4Namespace(), '\\'), ...$parts,
        ]))->filter()->implode('\\');
    }

    private function upgradeFolder(...$parts)
    {
        return $this->appFolder('Upgrades', ...$parts);
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
        return json_decode(
            File::get($this->folder.DIRECTORY_SEPARATOR.'composer.json'),
            true
        );
    }
}
