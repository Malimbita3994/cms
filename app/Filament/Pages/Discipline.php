<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesPageAccess;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Concerns\SingletonEditorActions;
use App\Filament\Support\NavigationGroups;
use App\Filament\Support\PortfolioFormFields;
use App\Models\DisciplinePage;
use App\Support\RichContentSanitizer;
use App\Support\SiteContentCache;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;

class Discipline extends Page implements HasForms
{
    use AuthorizesPageAccess;
    use InteractsWithForms;
    use InteractsWithPortfolioEditor;
    use SingletonEditorActions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Discipline';

    protected static ?int $navigationSort = 30;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::SITE_PAGES;

    protected static ?string $slug = 'discipline';

    protected string $view = 'filament.pages.discipline';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $duties = DisciplinePage::duties();
        $responsibility = DisciplinePage::professionalResponsibility();

        $this->form->fill([
            'duties_hero_eyebrow' => $duties?->hero_eyebrow ?? 'Discipline',
            'duties_hero_title' => $duties?->hero_title ?? 'System Analysis (Business Analysis) Duties',
            'duties_hero_description' => $duties?->hero_description ?? '',
            'duties_items' => collect($duties?->items ?? [])
                ->map(fn (string $text): array => ['text' => $text])
                ->values()
                ->all(),
            'duties_is_published' => $duties?->is_published ?? true,
            'responsibility_hero_eyebrow' => $responsibility?->hero_eyebrow ?? 'Discipline',
            'responsibility_hero_title' => $responsibility?->hero_title ?? 'Professional Responsibility',
            'responsibility_hero_description' => $responsibility?->hero_description ?? '',
            'responsibility_body' => $responsibility?->body ?? '',
            'responsibility_is_published' => $responsibility?->is_published ?? true,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Business analysis duties')
                    ->description('Public page: /discipline/duties — numbered duties list.')
                    ->extraAttributes(['class' => 'home-editor-card home-editor-card--main portfolio-stacked-editor-form'])
                    ->schema([
                        TextInput::make('duties_hero_eyebrow')->label('Eyebrow')->required(),
                        TextInput::make('duties_hero_title')->label('Page title')->required()->columnSpanFull(),
                        Textarea::make('duties_hero_description')
                            ->label('Introduction')
                            ->rows(3)
                            ->columnSpanFull(),
                        Repeater::make('duties_items')
                            ->label('Duties and responsibilities')
                            ->schema([
                                Textarea::make('text')
                                    ->label('Duty')
                                    ->required()
                                    ->rows(2),
                            ])
                            ->extraAttributes(['class' => 'home-editor-repeater'])
                            ->reorderable()
                            ->reorderableWithDragAndDrop()
                            ->itemHeaders(false)
                            ->collapsible(false)
                            ->minItems(1)
                            ->addActionLabel('Add duty')
                            ->columnSpanFull(),
                        Toggle::make('duties_is_published')
                            ->label('Published on public site')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Professional responsibility')
                    ->description('Public page: /discipline/professional-responsibility')
                    ->extraAttributes(['class' => 'home-editor-card home-editor-card--main portfolio-stacked-editor-form'])
                    ->schema([
                        TextInput::make('responsibility_hero_eyebrow')->label('Eyebrow')->required(),
                        TextInput::make('responsibility_hero_title')->label('Page title')->required()->columnSpanFull(),
                        Textarea::make('responsibility_hero_description')
                            ->label('Introduction')
                            ->rows(3)
                            ->columnSpanFull(),
                        PortfolioFormFields::richEditor('responsibility_body', 'Responsibility statement', 'Describe professional accountability, certifications, and QA expectations…')
                            ->columnSpanFull(),
                        Toggle::make('responsibility_is_published')
                            ->label('Published on public site')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $this->swalLoading('Saving discipline content…');

        $state = $this->form->getState();

        $dutyLines = collect($state['duties_items'] ?? [])
            ->map(fn (mixed $row): ?string => is_array($row) ? trim((string) ($row['text'] ?? '')) : trim((string) $row))
            ->filter()
            ->values()
            ->all();

        DisciplinePage::query()->updateOrCreate(
            ['slug' => DisciplinePage::SLUG_DUTIES],
            [
                'title' => 'Duties and Responsibilities',
                'hero_eyebrow' => $state['duties_hero_eyebrow'],
                'hero_title' => $state['duties_hero_title'],
                'hero_description' => $state['duties_hero_description'],
                'items' => $dutyLines,
                'body' => null,
                'is_published' => (bool) ($state['duties_is_published'] ?? true),
            ],
        );

        DisciplinePage::query()->updateOrCreate(
            ['slug' => DisciplinePage::SLUG_RESPONSIBILITY],
            [
                'title' => 'Professional Responsibility',
                'hero_eyebrow' => $state['responsibility_hero_eyebrow'],
                'hero_title' => $state['responsibility_hero_title'],
                'hero_description' => $state['responsibility_hero_description'],
                'items' => null,
                'body' => RichContentSanitizer::clean($state['responsibility_body'] ?? '') ?? '',
                'is_published' => (bool) ($state['responsibility_is_published'] ?? true),
            ],
        );

        SiteContentCache::flush();
        $this->queueSiteRevalidation();

        $this->swalSuccess(
            'Discipline content saved',
            'Duties and professional responsibility pages have been updated.',
        );
    }

    public function cancel(): void
    {
        $this->redirect(filament()->getUrl());
    }

    public function getHeader(): ?View
    {
        return null;
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return null;
    }
}
