# Filament 3 Guide

This package line targets Filament 5. If you are maintaining an older Filament 3 project, use the legacy Filament 3 package and flow documented below.

## Legacy Package

The Filament 3 integration lived in the older package line:

```bash
composer require InfinityXTech/filament-unlayer
```

## Model Cast

Store the editor state as an array:

```php
protected $casts = [
   'content' => 'array',
];
```

## Publishing

Publish the legacy config if needed:

```bash
php artisan vendor:publish --tag="filament-unlayer-config"
```

Optionally publish the views:

```bash
php artisan vendor:publish --tag="filament-unlayer-views"
```

## Basic Usage

Use the field like any other Filament form field:

```php
Unlayer::make('content')->required()
```

The Filament 3 package also provided a separate template select flow:

```php
SelectTemplate::make('template'),
Unlayer::make('content')->required()
```

If the Unlayer field name was changed from `content`, the template selector had to update that target field:

```php
SelectTemplate::make('template')
    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set)
        => $set('description', InfinityXTech\FilamentUnlayer\Services\GetTemplates::find($state))
    ),

Unlayer::make('description')->required()
```

You could also pass additional editor options:

```php
Unlayer::make('description')
    ->additionalOptions([
        'option' => 'value',
    ])
```

## Notes

- `SelectTemplate` extended the Filament `Select` field.
- `Unlayer` extended the Filament `Field` class.
- The Filament 3 package flow is different from the current Filament 5 package line.