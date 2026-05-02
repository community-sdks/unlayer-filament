<?php

namespace ZPMLabs\FilamentUnlayer\Forms\Components;

use Filament\Forms\Components\Field;

class Unlayer extends Field
{
    protected string $view = 'filament-unlayer::filament-unlayer';

    protected string $displayMode = 'email';

    /**
     * @var array<string, mixed>
     */
    protected array $additionalOptions = [];

    protected string $height = '70svh';

    protected bool $hasTemplatePicker = false;

    /**
     * @var array<string, mixed>
     */
    protected array $templatePickerOptions = [];

    /**
     * @var array<string, mixed>
     */
    protected array $templateSearchOptions = [];

    protected bool $syncLive = false;

    public static function make(?string $name = null): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();
        $static->columnSpanFull();

        return $static;
    }

    public function displayMode(string $mode): static
    {
        $this->displayMode = $mode;

        return $this;
    }

    public function height(string $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getDisplayMode(): string
    {
        return $this->displayMode;
    }

    public function getHeight(): string
    {
        return $this->height;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function templatePicker(bool $condition = true, array $options = []): static
    {
        $this->hasTemplatePicker = $condition;
        $this->templateSearchOptions = $options;

        return $this;
    }

    public function hasTemplatePicker(): bool
    {
        return $this->hasTemplatePicker;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTemplateSearchOptions(): array
    {
        if (! $this->hasTemplatePicker) {
            return [];
        }

        return array_merge([
            'search' => '',
            'type' => $this->displayMode,
            'premium' => false,
            'limit' => 20,
            'offset' => 0,
            'collection' => '',
            'sort' => 'recent',
        ], $this->templateSearchOptions);
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function templatePickerOptions(array $options): static
    {
        $this->templatePickerOptions = $options;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTemplatePickerOptions(): array
    {
        if (! $this->hasTemplatePicker) {
            return [
                'enabled' => false,
                'showTrigger' => false,
            ];
        }

        return array_merge([
            'enabled' => true,
            'showTrigger' => true,
            'label' => $this->getLabel() ?? 'Template Editor',
            'triggerLabel' => 'Search templates',
            'title' => 'Templates',
            'placeholder' => 'Search templates',
            'emptyText' => 'No templates found.',
        ], $this->templatePickerOptions);
    }

    /**
     * @param  array<string, mixed>  $additionalOptions
     */
    public function additionalOptions(array $additionalOptions): static
    {
        $this->additionalOptions = $additionalOptions;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAdditionalOptions(): array
    {
        return $this->additionalOptions;
    }

    public function syncLive(bool $condition = true): static
    {
        $this->syncLive = $condition;

        return $this;
    }

    public function shouldSyncLive(): bool
    {
        return $this->syncLive;
    }
}
