<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesPageAccess;
use App\Filament\Concerns\SingletonEditorActions;
use App\Filament\Support\NavigationGroups;
use App\Filament\Support\PortfolioEditorActions;
use App\Filament\Support\PortfolioFormFields;
use App\Filament\Support\PortfolioModalRows;
use App\Models\HomePage;
use App\Models\Profile;
use App\Support\CoreFocusItems;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;

class Home extends Page implements HasForms
{
    use AuthorizesPageAccess;
    use InteractsWithForms;
    use InteractsWithPortfolioEditor;
    use SingletonEditorActions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $navigationLabel = 'Home';

    protected static ?int $navigationSort = 2;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::CONTENT;

    protected static ?string $slug = 'home';

    protected string $view = 'filament.pages.home';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $home = HomePage::query()->first();
        $profile = Profile::query()->first();

        $strengths = $home?->core_focus_strengths ?? $profile?->strengths ?? [];
        $defaultBlurb = $home?->core_focus_blurb
            ?? 'Professional capability aligned with enterprise delivery outcomes.';

        $this->form->fill([
            'hero_background_image' => PortfolioAsset::toUploadState(
                $home?->hero_background_image ?? '/Home.jpg?v=2',
            ),
            'hero_profile_image' => PortfolioAsset::toUploadState(
                $home?->hero_profile_image ?? $profile?->image ?? '/profile-hd.png?v=7',
            ),
            'headline' => $home?->headline ?? $profile?->role ?? 'System Analyst',
            'hero_summary' => $home?->hero_summary ?? $profile?->summary,
            'core_focus_title' => $home?->core_focus_title ?? 'Core Focus',
            'core_focus_strengths' => CoreFocusItems::forForm($strengths, $defaultBlurb),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make()
                    ->extraAttributes(['class' => 'home-editor-card home-editor-card--main'])
                    ->schema([
                        Grid::make()
                            ->extraAttributes(['class' => 'home-editor-columns'])
                            ->columns([
                                'default' => 1,
                                'xl' => 2,
                            ])
                            ->schema([
                                Group::make([
                                    $this->portfolioImageUpload(
                                        'hero_background_image',
                                        'uploads/home/background',
                                        'Home background image',
                                        220,
                                        'Drag and drop to replace, or use the pencil icon to edit. Recommended: 1920×1080px or higher, JPG/PNG.',
                                    ),
                                    TextInput::make('headline')
                                        ->label('Headline')
                                        ->required()
                                        ->placeholder('System Analyst')
                                        ->helperText('e.g. System Analyst — the last word is highlighted in orange on the site.'),
                                    PortfolioFormFields::applyRichEditorDefaults(
                                        RichEditor::make('hero_summary')
                                            ->label('Introduction')
                                            ->required()
                                            ->placeholder('Brief professional summary for the homepage…')
                                            ->helperText('A short introduction displayed on the homepage.'),
                                    )
                                ])
                                    ->extraAttributes(['class' => 'home-editor-col home-editor-col--left']),
                                Group::make([
                                    $this->portfolioImageUpload(
                                        'hero_profile_image',
                                        'uploads/home/front',
                                        'Front image',
                                        160,
                                        'Drag and drop to replace, or use the pencil icon to edit. Recommended square or portrait, JPG/PNG.',
                                    ),
                                    TextInput::make('core_focus_title')
                                        ->label('Core focus — title')
                                        ->required()
                                        ->default('Core Focus')
                                        ->helperText('Title for the core focus section.'),
                                    Repeater::make('core_focus_strengths')
                                        ->label('Core focus — items')
                                        ->schema([
                                            TextInput::make('heading')
                                                ->label('Heading')
                                                ->required()
                                                ->placeholder('e.g. System Architecture Design'),
                                            PortfolioFormFields::applyRichEditorDefaults(
                                                RichEditor::make('text')
                                                    ->label('Text')
                                                    ->required(),
                                            )
                                                ->placeholder('Short description shown under this heading on the homepage.'),
                                        ])
                                        ->extraAttributes(['class' => 'home-editor-repeater'])
                                        ->reorderable()
                                        ->reorderableWithDragAndDrop()
                                        ->itemHeaders(true)
                                        ->collapsible(false)
                                        ->cloneable()
                                        ->deletable()
                                        ->deleteAction(
                                            PortfolioEditorActions::confirmedDelete(
                                                'Remove core focus item?',
                                                'This item will be removed from the homepage Core Focus section.',
                                            ),
                                        )
                                        ->defaultItems(2)
                                        ->minItems(1)
                                        ->maxItems(8)
                                        ->addActionLabel('Add item')
                                        ->helperText('Drag to reorder. Use the trash icon to remove an item (at least one required).'),
                                ])
                                    ->extraAttributes(['class' => 'home-editor-col home-editor-col--right']),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $this->swalLoading('Saving homepage…');

        $state = $this->form->getState();
        $existingHome = HomePage::query()->first();
        $coreFocusStrengths = CoreFocusItems::forStorage($state['core_focus_strengths'] ?? []);

        $payload = [
            'hero_background_image' => PortfolioAsset::toPublicPath($state['hero_background_image'] ?? null)
                ?? '/Home.jpg?v=2',
            'hero_profile_image' => PortfolioAsset::toPublicPath($state['hero_profile_image'] ?? null)
                ?? '/profile-hd.png?v=7',
            'headline' => $state['headline'],
            'hero_summary' => RichContentSanitizer::clean($state['hero_summary'] ?? '') ?? '',
            'core_focus_title' => $state['core_focus_title'],
            'core_focus_strengths' => $coreFocusStrengths,
            'core_focus_blurb' => $this->resolveCoreFocusBlurb($coreFocusStrengths, $existingHome?->core_focus_blurb),
            'show_core_focus' => true,
        ];

        $home = $existingHome;

        if ($home) {
            $home->update($payload);
        } else {
            HomePage::query()->create(array_merge($payload, [
                'availability_prefix' => 'Available for projects',
                'primary_cta_label' => 'Start a Project',
                'primary_cta_url' => '/contact',
                'secondary_cta_label' => 'View my Works',
                'secondary_cta_url' => '#projects',
                'cv_cta_label' => 'View CV',
                'projects_stat_title' => 'Projects',
                'projects_stat_subtitle' => 'Delivered systems',
                'services_stat_title' => 'Services',
                'services_stat_subtitle' => 'Core offerings',
                'insights_stat_title' => 'Insights',
                'insights_stat_subtitle' => 'Published notes',
            ]));
        }

        $this->queueSiteRevalidation();

        $this->swalSuccess(
            'Home page saved',
            'Your homepage content has been updated on the live site.',
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->singletonEditorActionGroup(
                'Home page',
                fn (): ?HomePage => HomePage::query()->first(),
                fn (HomePage $record): string => $record->headline ?: 'Home page',
                fn (HomePage $record) => view('filament.partials.portfolio-record-modal', PortfolioModalRows::home($record)),
            ),
        ];
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

    /**
     * @param  array<int, array{heading: string, text: string}>  $stored
     */
    protected function resolveCoreFocusBlurb(array $stored, ?string $existing): string
    {
        $firstText = $stored[0]['text'] ?? '';

        if ($firstText !== '') {
            return $firstText;
        }

        return RichContentSanitizer::clean($existing ?? '')
            ?? 'Professional capability aligned with enterprise delivery outcomes.';
    }
}
