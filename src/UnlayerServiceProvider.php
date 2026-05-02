<?php

namespace ZPMLabs\FilamentUnlayer;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ZPMLabs\FilamentUnlayer\Examples\Database\Seeders\FilamentUnlayerDemoSeeder;
use ZPMLabs\LaravelPackageQuickDemo\Facades\QuickDemo;
use ZPMLabs\LaravelPackageQuickDemo\Support\DemoDefinition;

class UnlayerServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-unlayer';

    public static string $viewNamespace = 'filament-unlayer';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
        */
        $package->name(static::$name);

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        $this->registerQuickDemo();
    }

    protected function registerQuickDemo(): void
    {
        QuickDemo::register(
            DemoDefinition::make(
                key: 'filament-unlayer-demo',
                name: 'Filament Unlayer Demo',
                databaseName: 'filament_unlayer_demo',
                migrationsPath: __DIR__ . '/../examples/migrations',
                seeders: [
                    FilamentUnlayerDemoSeeder::class,
                ],
                viewsPath: __DIR__ . '/../examples/views',
            )
        );
    }
}
