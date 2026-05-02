<?php

use CommunitySdks\UnlayerFilament\Examples\Database\Seeders\UnlayerFilamentDemoSeeder;
use CommunitySdks\UnlayerFilament\Examples\Models\DemoNewsletterTemplate;
use CommunitySdks\UnlayerFilament\Examples\Pages\UnlayerFilamentDemoPage;
use CommunitySdks\UnlayerFilament\Forms\Components\Unlayer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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
        ->and($composer['name'])->toBe('community-sdks/unlayer-filament')
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
    $demo = QuickDemo::get('unlayer-filament-demo');

    expect($demo->key)->toBe('unlayer-filament-demo')
        ->and($demo->name)->toBe('Unlayer Filament Demo')
        ->and($demo->databaseName)->toBe('unlayer_filament_demo')
        ->and($demo->connectionName())->toBe('quick_demo_unlayer_filament_demo')
        ->and($demo->routesPath)->toBeNull()
        ->and($demo->migrationsPath)->toEndWith('examples/migrations');
});

it('uses the quick demo connection for demo editor state', function () {
    $model = new DemoNewsletterTemplate;

    expect($model->getConnectionName())->toBe('quick_demo_unlayer_filament_demo')
        ->and($model->getTable())->toBe('demo_newsletter_templates');
});

it('renders only the active quick demo tab panel', function () {
    $page = new UnlayerFilamentDemoPage;

    expect($page->activeTab)->toBe('email')
        ->and(file_get_contents(__DIR__ . '/../examples/Pages/UnlayerFilamentDemoPage.php'))
        ->toContain("->livewireProperty('activeTab')")
        ->toContain("#[Url(as: 'example')]");
});

it('syncs editor exports back to the filament field state', function () {
    $view = file_get_contents(__DIR__ . '/../resources/views/unlayer-filament.blade.php');

    expect($view)
        ->toContain('x-on:unlayer-livewire:exported.window')
        ->toContain(':state="$getState()"')
        ->toContain('window.Livewire.find(@js($this->getId())).$set(@js($getStatePath()), $event.detail.state, @js($shouldSyncLive()))');
});

it('migrates and seeds the unlayer demo database', function () {
    $databasePath = QuickDemo::databasePath('unlayer-filament-demo');
    $connectionName = QuickDemo::connectionName('unlayer-filament-demo');
    $migration = include __DIR__ . '/../examples/migrations/2026_01_01_000001_create_demo_newsletter_templates_table.php';
    $migrated = false;

    File::ensureDirectoryExists(dirname($databasePath));
    File::delete($databasePath);
    touch($databasePath);
    DB::purge($connectionName);

    try {
        $migration->up();
        $migrated = true;

        (new UnlayerFilamentDemoSeeder)->run();
        (new UnlayerFilamentDemoSeeder)->run();

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
