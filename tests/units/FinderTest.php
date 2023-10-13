<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use LaravelLiberu\TestUpgrade\Upgrades\Deep\DeepUpgrade;
use LaravelLiberu\TestUpgrade\Upgrades\POPO;
use LaravelLiberu\TestUpgrade\Upgrades\SimpleUpgrade;
use LaravelLiberu\TestUpgrade\Upgrades\StructureUpgrade;
use LaravelLiberu\Upgrade\Contracts\MigratesStructure;
use LaravelLiberu\Upgrade\Services\Finder;
use LaravelLiberu\Upgrade\Services\Structure;
use Tests\TestCase;

class FinderTest extends TestCase
{
    use RefreshDatabase;

    protected MigratesStructure $upgrade;
    protected $defaultRole;
    protected $secondaryRole;

    public function setUp(): void
    {
        parent::setUp();

        File::copyDirectory(__DIR__.'/../stubs', $this->package());
        $this->register();
        Config::set('liberu.upgrade.folders', ['vendor/laravel-liberu/testUpgrades']);
        Config::set('liberu.upgrade.vendors', []);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        File::deleteDirectory($this->package());
    }

    /** @test */
    public function should_not_find_classes_dont_implement_contracts()
    {
        $this->assertEmpty($this->getUpgrade(POPO::class));
    }

    /** @test */
    public function can_find_structure_upgrade()
    {
        $structureUpgrade = $this->getUpgrade(Structure::class)
            ->first()->reflection()->getName();

        $this->assertEquals(StructureUpgrade::class, $structureUpgrade);
    }

    /** @test */
    public function can_find_regular_upgrade()
    {
        $this->assertNotEmpty($this->getUpgrade(SimpleUpgrade::class));
    }

    /** @test */
    public function can_find_deep_upgrade()
    {
        $this->assertNotEmpty($this->getUpgrade(DeepUpgrade::class));
    }

    protected function register(): void
    {
        $loader = require base_path().'/vendor/autoload.php';
        $loader->setPsr4(
            'LaravelLiberu\TestUpgrade\\',
            $this->package('src')
        );
    }

    protected function getUpgrade(string $class): Collection
    {
        return (new Finder())->upgrades()
            ->filter(fn ($upgrade) => $upgrade::class === $class);
    }

    private function package(...$folders): string
    {
        $relative = Collection::wrap($folders)
            ->prepend('vendor/laravel-liberu/testUpgrades')
            ->implode('/');

        return base_path($relative);
    }
}
