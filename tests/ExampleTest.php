<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZPMLabs\FilamentUnlayer\Examples\Database\Seeders\FilamentUnlayerDemoSeeder;
use ZPMLabs\FilamentUnlayer\Examples\Models\DemoNewsletterTemplate;
use ZPMLabs\FilamentUnlayer\Examples\Pages\FilamentUnlayerDemoPage;
use ZPMLabs\FilamentUnlayer\Forms\Components\Unlayer;
use ZPMLabs\LaravelPackageQuickDemo\Facades\QuickDemo;

it('configures the unlayer field defaults', function () {
    $field = Unlayer::make('content');

    expect($field)
        ->getDisplayMode()->toBe('email')
        ->getHeight()->toBe('70svh')
        ->getAdditionalOptions()->toBe([])
        ->hasTemplatePicker()->toBeFalse()
        ->shouldSyncLive()->toBeFalse();
});

it('allows live syncing to be enabled explicitly', function () {
    $field = Unlayer::make('content')
        ->syncLive();

    expect($field->shouldSyncLive())->toBeTrue();
});

it('supports custom unlayer options', function () {
    $field = Unlayer::make('description')
        ->displayMode('page')
        ->height('600px')
        ->additionalOptions([
            'projectId' => 123,
        ]);

    expect($field)
        ->getDisplayMode()->toBe('page')
        ->getHeight()->toBe('600px')
        ->getAdditionalOptions()->toBe([
            'projectId' => 123,
        ]);
});

it('passes template picker options to the livewire editor', function () {
    $field = Unlayer::make('content')
        ->displayMode('web')
        ->templatePicker(options: [
            'limit' => 12,
            'sort' => 'popular',
        ]);

    expect($field)
        ->hasTemplatePicker()->toBeTrue()
        ->getTemplateSearchOptions()->toMatchArray([
            'search' => '',
            'type' => 'web',
            'premium' => false,
            'limit' => 12,
            'offset' => 0,
            'collection' => '',
            'sort' => 'popular',
        ])
        ->getTemplatePickerOptions()->toMatchArray([
            'enabled' => true,
            'showTrigger' => true,
            'triggerLabel' => 'Search templates',
        ]);
});

it('requires filament v5 packages', function () {
    $composer = json_decode(file_get_contents(__DIR__ . '/../composer.json'), true);

    expect($composer)->not->toHaveKey('version')
        ->and($composer['require']['php'])->toBe('^8.3')
        ->and($composer['require']['community-sdks/unlayer-livewire'])->toBe('^1.0')
        ->and($composer['require']['filament/filament'])->toBe('^5.0')
        ->and($composer['require'])->not->toHaveKey('filament/forms')
        ->and($composer['require']['zpmlabs/laravel-package-quick-demo'])->toBe('^1.0')
        ->and($composer['require-dev'])->toHaveKey('larastan/larastan')
        ->and($composer['require-dev'])->not->toHaveKey('nunomaduro/collision')
        ->and($composer['require-dev'])->not->toHaveKey('nunomaduro/larastan')
        ->and($composer['require-dev'])->not->toHaveKey('phpstan/extension-installer')
        ->and($composer['require-dev'])->not->toHaveKey('spatie/laravel-ray')
        ->and($composer['require-dev']['orchestra/testbench'])->toBe('^11.0')
        ->and($composer['require-dev']['pestphp/pest'])->toBe('^4.4');
});

it('registers the quick demo definition', function () {
    $demo = QuickDemo::get('filament-unlayer-demo');

    expect($demo->key)->toBe('filament-unlayer-demo')
        ->and($demo->name)->toBe('Filament Unlayer Demo')
        ->and($demo->databaseName)->toBe('filament_unlayer_demo')
        ->and($demo->connectionName())->toBe('quick_demo_filament_unlayer_demo')
        ->and($demo->routesPath)->toBeNull()
        ->and($demo->migrationsPath)->toEndWith('examples/migrations');
});

it('uses the quick demo connection for demo editor state', function () {
    $model = new DemoNewsletterTemplate;

    expect($model->getConnectionName())->toBe('quick_demo_filament_unlayer_demo')
        ->and($model->getTable())->toBe('demo_newsletter_templates');
});

it('renders only the active quick demo tab panel', function () {
    $page = new FilamentUnlayerDemoPage;

    expect($page->activeTab)->toBe('email')
        ->and(file_get_contents(__DIR__ . '/../examples/Pages/FilamentUnlayerDemoPage.php'))
        ->toContain("->livewireProperty('activeTab')")
        ->toContain("#[Url(as: 'example')]");
});

it('syncs editor exports back to the filament field state', function () {
    $view = file_get_contents(__DIR__ . '/../resources/views/filament-unlayer.blade.php');

    expect($view)
        ->toContain('x-on:unlayer-livewire:exported.window')
        ->toContain(':state="$getState()"')
        ->toContain('window.Livewire.find(@js($this->getId())).$set(@js($getStatePath()), $event.detail.state, @js($shouldSyncLive()))');
});

it('migrates and seeds the unlayer demo database', function () {
    $databasePath = QuickDemo::databasePath('filament-unlayer-demo');
    $connectionName = QuickDemo::connectionName('filament-unlayer-demo');
    $migration = include __DIR__ . '/../examples/migrations/2026_01_01_000001_create_demo_newsletter_templates_table.php';
    $migrated = false;

    File::ensureDirectoryExists(dirname($databasePath));
    File::delete($databasePath);
    touch($databasePath);
    DB::purge($connectionName);

    try {
        $migration->up();
        $migrated = true;

        (new FilamentUnlayerDemoSeeder)->run();
        (new FilamentUnlayerDemoSeeder)->run();

        $template = DemoNewsletterTemplate::query()->first();

        expect(DemoNewsletterTemplate::query()->count())->toBe(1)
            ->and($template)->not->toBeNull()
            ->and($template->email_content)->toBeArray()
            ->and($template->page_content)->toBeArray()
            ->and($template->template_content)->toBeArray()
            ->and($template->custom_options_content)->toBeArray();
    } finally {
        if ($migrated) {
            $migration->down();
        }

        DB::purge($connectionName);
        File::delete($databasePath);
    }
});
