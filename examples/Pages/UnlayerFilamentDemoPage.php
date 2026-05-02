<?php

namespace CommunitySdks\UnlayerFilament\Examples\Pages;

use CommunitySdks\UnlayerFilament\Examples\Models\DemoNewsletterTemplate;
use CommunitySdks\UnlayerFilament\Forms\Components\Unlayer;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Livewire\Attributes\Url;

/**
 * @property Schema $content
 */
class UnlayerFilamentDemoPage extends Page
{
    protected string $view = 'quick-demo-unlayer-filament-demo::page';

    protected static ?string $title = 'Unlayer Filament Demo';

    protected static bool $shouldRegisterNavigation = true;

    /**
     * @var array<string, mixed>
     */
    public array $data = [];

    #[Url(as: 'example')]
    public string $activeTab = 'email';

    public ?int $recordId = null;

    public function mount(): void
    {
        $record = DemoNewsletterTemplate::query()->first()
            ?? DemoNewsletterTemplate::query()->create([
                'email_content' => $this->blankDesign(),
                'page_content' => $this->blankDesign(),
            ]);

        $this->fillRecord($record);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->columns(1)
            ->components([
                Tabs::make('Unlayer examples')
                    ->livewireProperty('activeTab')
                    ->tabs([
                        'email' => Tab::make('Email editor')
                            ->schema([
                                Section::make('Email design')
                                    ->schema([
                                        Unlayer::make('email_content')
                                            ->height('65svh')
                                            ->templatePicker()
                                            ->required(),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        'page' => Tab::make('Page mode')
                            ->schema([
                                Section::make('Landing page design')
                                    ->schema([
                                        Unlayer::make('page_content')
                                            ->displayMode('web')
                                            ->height('65svh')
                                            ->templatePicker(),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $state = $this->content->getState();

        $record = DemoNewsletterTemplate::query()->updateOrCreate(
            ['id' => $this->recordId],
            $state,
        );

        $this->fillRecord($record);

        Notification::make()
            ->title('Newsletter template saved')
            ->success()
            ->send();
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->action('save')
                ->keyBindings(['mod+s']),
        ];
    }

    private function fillRecord(DemoNewsletterTemplate $record): void
    {
        $this->recordId = $record->getKey();

        $this->content->fill([
            'email_content' => $record->email_content ?: $this->blankDesign(),
            'page_content' => $record->page_content ?: $this->blankDesign(),
        ]);
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
