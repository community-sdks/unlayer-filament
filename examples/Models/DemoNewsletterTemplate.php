<?php

namespace CommunitySdks\FilamentUnlayer\Examples\Models;

use Illuminate\Database\Eloquent\Model;
use ZPMLabs\LaravelPackageQuickDemo\Facades\QuickDemo;

/**
 * @property array<string, mixed>|null $email_content
 * @property array<string, mixed>|null $page_content
 * @property array<string, mixed>|null $template_content
 * @property array<string, mixed>|null $custom_options_content
 */
class DemoNewsletterTemplate extends Model
{
    protected $table = 'demo_newsletter_templates';

    protected $guarded = [];

    public function getConnectionName(): string
    {
        return QuickDemo::connectionName('filament-unlayer-demo');
    }

    protected function casts(): array
    {
        return [
            'email_content' => 'array',
            'page_content' => 'array',
            'template_content' => 'array',
            'custom_options_content' => 'array',
        ];
    }
}
