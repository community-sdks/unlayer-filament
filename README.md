# Unlayer Filament

![image](https://github.com/user-attachments/assets/92204605-3edf-48ba-81a8-0eadce20b2c5)


This is a Filament field wrapper for the Unlayer editor. It delegates the editor runtime to [`community-sdks/unlayer-livewire`](https://packagist.org/packages/community-sdks/unlayer-livewire), which wraps the Alpine and TypeScript SDK packages.

## Try The Example

If you want to try the package quickly in a Laravel app, install it and then install the bundled quick demo:

This package ships its example through [`zpmlabs/laravel-package-quick-demo`](https://packagist.org/packages/zpmlabs/laravel-package-quick-demo). The quick demo installer sets up an isolated demo environment for the package so you can try the field without manually wiring routes, database connections, migrations, seeders, or demo views into your main application.

```bash
composer require community-sdks/unlayer-filament
php artisan quick-demo:install unlayer-filament-demo
```

To inspect the registered demo or see its route details:

```bash
php artisan quick-demo:show unlayer-filament-demo
```

The bundled `Unlayer Filament Demo` includes an email editor tab and a web/page editor tab, both backed by isolated quick-demo data.

## Requirements

- PHP 8.3+
- Laravel 13+
- Filament 5.x

## Installation

You can install the package via composer:

```bash
composer require community-sdks/unlayer-filament
```

### Install The Livewire Browser Asset

The Filament field depends on the browser asset published by `community-sdks/unlayer-livewire`.

Install it with:

```bash
php artisan unlayer-livewire:install
```

This publishes the compiled browser file to:

```txt
public/unlayer-livewire.js
```

After upgrading the Livewire package or rebuilding that browser asset locally, publish the latest file again:

```bash
php artisan vendor:publish --tag=unlayer-livewire-assets --force
```

If you also want to publish the Livewire config to customize upload storage or other package options:

```bash
php artisan unlayer-livewire:install --config
```

To overwrite previously published files during install:

```bash
php artisan unlayer-livewire:install --force
```

You can also publish assets or config manually:

```bash
php artisan vendor:publish --tag=unlayer-livewire-assets --force
php artisan vendor:publish --tag=unlayer-livewire-config
```

### Version Compatibility

This package targets Filament 5.x only.

- **Filament 5.x**: use this package line, `composer require community-sdks/unlayer-filament`

Create a cast within your model:

```php
protected $casts = [
   'content' => 'array',
];
```

The field stores the editor state as an array containing both `html` and `design`.

If you want to customize uploads or the template proxy route prefix, publish the Livewire package config:

```bash
php artisan vendor:publish --tag="unlayer-livewire-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="unlayer-filament-views"
```

## Usage

As any other filament form field:

```php
Unlayer::make('content')->required()
```

In case you want users to choose Unlayer stock templates from inside the editor, enable the template picker:

```php
Unlayer::make('content')
    ->required()
    ->templatePicker()
```

The picker loads templates through the Livewire package's same-origin backend proxy and applies the selected design directly to the initialized editor.

You can customize the default stock template query:

```php
Unlayer::make('content')
    ->templatePicker(options: [
        'type' => 'email', // or 'web'
        'premium' => false,
        'limit' => 20,
        'offset' => 0,
        'collection' => '',
        'sort' => 'recent',
    ])
```

You can customize the picker toolbar and panel labels:

```php
Unlayer::make('content')
    ->templatePicker()
    ->templatePickerOptions([
        'label' => 'Template Editor',
        'triggerLabel' => 'Search templates',
        'title' => 'Templates',
        'placeholder' => 'Search templates',
        'emptyText' => 'No templates found.',
    ])
```

Live syncing is disabled by default. If you want every editor change to sync immediately through Livewire, enable it explicitly:

```php
Unlayer::make('content')
    ->syncLive()
```

If you want to pass additional options to unlayer, which will join default object set by plugin with your additional data you can use:

```php
Unlayer::make('description')
    ->additionalOptions([
        'option' => 'value'
    ])
```

The built-in Livewire proxy exists because Unlayer's stock template endpoints do not allow direct browser CORS requests. Behind the proxy, template search calls:

```txt
POST https://unlayer.com/templates/search
Content-Type: application/json
```

The field's `templatePicker(options: [...])` values map to the upstream request body like this:

```txt
search     -> filter.name
type       -> filter.type
premium    -> filter.premium, "true" when true, "" when false
limit      -> perPage
offset     -> page, calculated as floor(offset / limit) + 1
collection -> filter.collection
sort       -> filter.sortBy
```

Selected templates are loaded through:

```txt
POST https://studio.unlayer.com/api/v1/graphql
```

The Livewire package also supports overriding its route prefix and middleware in `config/unlayer-livewire.php`.

`Unlayer` is extending filament `Field` class.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
