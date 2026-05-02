<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use ZPMLabs\LaravelPackageQuickDemo\Facades\QuickDemo;

return new class extends Migration
{
    public function getConnection(): string
    {
        return QuickDemo::connectionName('filament-unlayer-demo');
    }

    public function up(): void
    {
        Schema::connection($this->getConnection())->create('demo_newsletter_templates', function (Blueprint $table): void {
            $table->id();
            $table->json('email_content')->nullable();
            $table->json('page_content')->nullable();
            $table->json('template_content')->nullable();
            $table->json('custom_options_content')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection($this->getConnection())->dropIfExists('demo_newsletter_templates');
    }
};
