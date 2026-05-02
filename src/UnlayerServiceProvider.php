<?php

namespace CommunitySdks\UnlayerFilament;

use CommunitySdks\UnlayerFilament\Examples\Database\Seeders\UnlayerFilamentDemoSeeder;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ZPMLabs\LaravelPackageQuickDemo\Facades\QuickDemo;
use ZPMLabs\LaravelPackageQuickDemo\Support\DemoDefinition;

class UnlayerServiceProvider extends PackageServiceProvider
{
    public static string $name = 'unlayer-filament';

    public static string $viewNamespace = 'unlayer-filament';

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
                key: 'unlayer-filament-demo',
                name: 'Unlayer Filament Demo',
                databaseName: 'unlayer_filament_demo',
                migrationsPath: __DIR__ . '/../examples/migrations',
                seeders: [
                    UnlayerFilamentDemoSeeder::class,
                ],
                viewsPath: __DIR__ . '/../examples/views',
            )
        );
    }
}
