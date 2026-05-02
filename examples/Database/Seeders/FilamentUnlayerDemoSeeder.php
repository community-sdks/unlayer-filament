<?php

namespace CommunitySdks\FilamentUnlayer\Examples\Database\Seeders;

use CommunitySdks\FilamentUnlayer\Examples\Models\DemoNewsletterTemplate;
use Illuminate\Database\Seeder;

class FilamentUnlayerDemoSeeder extends Seeder
{
    public function run(): void
    {
        DemoNewsletterTemplate::query()->updateOrCreate(
            ['id' => 1],
            [
                'email_content' => $this->blankDesign(),
                'page_content' => $this->blankDesign(),
                'template_content' => $this->blankDesign(),
                'custom_options_content' => $this->blankDesign(),
            ],
        );
    }

    /**
     * @return array{html: string, design: array<string, mixed>}
     */
    private function blankDesign(): array
    {
        return [
            'html' => '',
            'design' => [],
        ];
    }
}
