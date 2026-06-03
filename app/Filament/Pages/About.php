<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Concerns\SingletonEditorActions;
use App\Filament\Support\PortfolioEditorActions;
use App\Filament\Support\PortfolioFormFields;
use App\Filament\Support\PortfolioModalRows;
use App\Filament\Concerns\AuthorizesPageAccess;
use App\Filament\Support\NavigationGroups;
use App\Models\Profile;
use App\Support\AboutContentItems;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class About extends Page implements HasForms
{
    use AuthorizesPageAccess;
    use InteractsWithForms;
    use InteractsWithPortfolioEditor;
    use SingletonEditorActions;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?string $navigationLabel = 'About';

    protected static ?int $navigationSort = 3;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::CONTENT;

    protected static ?string $slug = 'profiles';

    protected string $view = 'filament.pages.about';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $profile = Profile::query()->first();

        if (! $profile) {
            return;
        }

        $defaultStrengths = [
            ['title' => 'System Architecture', 'description' => 'Designing scalable, resilient enterprise systems.'],
            ['title' => 'API & ESB Integration', 'description' => 'Seamless integration across platforms and services.'],
            ['title' => 'Data Management', 'description' => 'Building reporting-ready and governed systems.'],
            ['title' => 'Digital Transformation', 'description' => 'Driving innovation in public sector systems.'],
        ];

        $defaultApproach = [
            '01 - Understand business needs deeply',
            '02 - Design scalable system architecture',
            '03 - Deliver secure and integrated systems',
            '04 - Continuously improve performance',
        ];

        $defaultValues = [
            ['title' => 'Integrity', 'icon' => 'Shield'],
            ['title' => 'Innovation', 'icon' => 'Lightbulb'],
            ['title' => 'Excellence', 'icon' => 'Trophy'],
            ['title' => 'Impact', 'icon' => 'Target'],
            ['title' => 'Collaboration', 'icon' => 'Handshake'],
            ['title' => 'Accountability', 'icon' => 'ShieldCheck'],
            ['title' => 'Service', 'icon' => 'HeartHandshake'],
            ['title' => 'Growth Mindset', 'icon' => 'Rocket'],
        ];

        $this->form->fill([
            'image' => PortfolioAsset::toUploadState($profile->image ?? '/profile-hd.png?v=7'),
            'name' => $profile->name,
            'role' => $profile->role,
            'tagline' => $profile->tagline,
            'summary' => $profile->summary,
            'email' => $profile->email,
            'phone' => $profile->phone,
            'location' => $profile->location,
            'linkedin_url' => $profile->linkedin_url,
            'github_url' => $profile->github_url,
            'about_eyebrow' => $profile->about_eyebrow ?? 'About Me',
            'about_heading_lead' => $profile->about_heading_lead ?? 'Driving Digital Transformation Through',
            'about_heading_accent' => $profile->about_heading_accent ?? 'Smart Systems',
            'about_strengths' => AboutContentItems::strengthsForForm(
                $profile->about_strengths ?? $defaultStrengths,
            ),
            'about_approach_steps' => AboutContentItems::approachForForm(
                $profile->about_approach_steps ?? $defaultApproach,
            ),
            'about_values' => AboutContentItems::valuesForForm(
                $profile->about_values ?? $defaultValues,
            ),
            'about_page_hero_eyebrow' => $profile->about_page_hero_eyebrow ?? 'About',
            'about_page_hero_title' => $profile->about_page_hero_title ?? 'Professional Profile',
            'about_page_hero_description' => $profile->about_page_hero_description
                ?? 'A deeper look at experience, strengths, values, and delivery philosophy.',
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
                                        'image',
                                        'uploads/about/profile',
                                        'Profile image',
                                        200,
                                        'Shown in the About section on the homepage and /about page.',
                                    ),
                                    TextInput::make('name')
                                        ->label('Name')
                                        ->required(),
                                    TextInput::make('role')
                                        ->label('Role / title')
                                        ->required()
                                        ->helperText('Shown under your photo and across the site.'),
                                    Textarea::make('tagline')
                                        ->label('Tagline')
                                        ->rows(2)
                                        ->required(),
                                    PortfolioFormFields::applyRichEditorDefaults(
                                        RichEditor::make('summary')
                                            ->label('Summary')
                                            ->required(),
                                    )
                                        ->helperText('Main paragraph in the About section on the homepage.'),
                                ])
                                    ->extraAttributes(['class' => 'home-editor-col home-editor-col--left']),
                                Group::make([
                                    TextInput::make('about_eyebrow')
                                        ->label('Section eyebrow')
                                        ->required()
                                        ->default('About Me'),
                                    TextInput::make('about_heading_lead')
                                        ->label('Heading — lead text')
                                        ->required()
                                        ->placeholder('Driving Digital Transformation Through'),
                                    TextInput::make('about_heading_accent')
                                        ->label('Heading — accent (highlighted)')
                                        ->required()
                                        ->placeholder('Smart Systems')
                                        ->helperText('The accent phrase is styled in blue on the public site.'),
                                    TextInput::make('email')
                                        ->label('Email')
                                        ->email()
                                        ->required(),
                                    TextInput::make('phone')
                                        ->label('Phone')
                                        ->tel(),
                                    TextInput::make('location')
                                        ->label('Location'),
                                    TextInput::make('linkedin_url')
                                        ->label('LinkedIn URL')
                                        ->url(),
                                    TextInput::make('github_url')
                                        ->label('GitHub URL')
                                        ->url(),
                                ])
                                    ->extraAttributes(['class' => 'home-editor-col home-editor-col--right']),
                                Repeater::make('about_strengths')
                                    ->label('Key strengths')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Heading')
                                            ->required(),
                                        Textarea::make('description')
                                            ->label('Text')
                                            ->rows(2)
                                            ->required(),
                                    ])
                                    ->extraAttributes(['class' => 'home-editor-repeater'])
                                    ->reorderable()
                                    ->reorderableWithDragAndDrop()
                                    ->itemHeaders(true)
                                    ->collapsible(false)
                                    ->deletable()
                                    ->deleteAction(
                                        PortfolioEditorActions::confirmedDelete(
                                            'Remove strength?',
                                            'This strength will be removed from the About section.',
                                        ),
                                    )
                                    ->minItems(1)
                                    ->maxItems(8)
                                    ->addActionLabel('Add strength')
                                    ->columnSpanFull()
                                    ->helperText('Cards in the “Key Strengths” column on the homepage About block.'),
                                Repeater::make('about_approach_steps')
                                    ->label('My approach — steps')
                                    ->schema([
                                        TextInput::make('step')
                                            ->label('Step')
                                            ->required()
                                            ->placeholder('01 - Understand business needs deeply'),
                                    ])
                                    ->extraAttributes(['class' => 'home-editor-repeater'])
                                    ->reorderable()
                                    ->reorderableWithDragAndDrop()
                                    ->itemHeaders(true)
                                    ->collapsible(false)
                                    ->deletable()
                                    ->minItems(1)
                                    ->maxItems(8)
                                    ->addActionLabel('Add step')
                                    ->columnSpanFull(),
                                Repeater::make('about_values')
                                    ->label('What I value')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Value')
                                            ->required(),
                                        Select::make('icon')
                                            ->label('Icon')
                                            ->options(AboutContentItems::valueIconOptions())
                                            ->required()
                                            ->native(false),
                                    ])
                                    ->extraAttributes(['class' => 'home-editor-repeater'])
                                    ->reorderable()
                                    ->reorderableWithDragAndDrop()
                                    ->itemHeaders(true)
                                    ->collapsible(false)
                                    ->deletable()
                                    ->minItems(1)
                                    ->maxItems(12)
                                    ->addActionLabel('Add value')
                                    ->columnSpanFull()
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
                Section::make('About page header')
                    ->description('Hero at the top of /about (separate from the homepage About block).')
                    ->extraAttributes(['class' => 'home-editor-card'])
                    ->schema([
                        TextInput::make('about_page_hero_eyebrow')
                            ->label('Eyebrow')
                            ->required(),
                        TextInput::make('about_page_hero_title')
                            ->label('Title')
                            ->required(),
                        Textarea::make('about_page_hero_description')
                            ->label('Description')
                            ->rows(3)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $this->swalLoading('Saving about content…');

        $profile = Profile::query()->first();

        if (! $profile) {
            $this->swalError(
                'No profile record',
                'Run database seeders to create the initial profile.',
            );

            return;
        }

        $state = $this->form->getState();

        $profile->update([
            'image' => PortfolioAsset::toPublicPath($state['image'] ?? null) ?? '/profile-hd.png?v=7',
            'name' => $state['name'],
            'role' => $state['role'],
            'tagline' => $state['tagline'],
            'summary' => RichContentSanitizer::clean($state['summary'] ?? '') ?? '',
            'email' => $state['email'],
            'phone' => $state['phone'] ?? null,
            'location' => $state['location'] ?? null,
            'linkedin_url' => $state['linkedin_url'] ?? null,
            'github_url' => $state['github_url'] ?? null,
            'about_eyebrow' => $state['about_eyebrow'],
            'about_heading_lead' => $state['about_heading_lead'],
            'about_heading_accent' => $state['about_heading_accent'],
            'about_strengths' => AboutContentItems::strengthsForStorage($state['about_strengths'] ?? []),
            'about_approach_steps' => AboutContentItems::approachForStorage($state['about_approach_steps'] ?? []),
            'about_values' => AboutContentItems::valuesForStorage($state['about_values'] ?? []),
            'about_page_hero_eyebrow' => $state['about_page_hero_eyebrow'],
            'about_page_hero_title' => $state['about_page_hero_title'],
            'about_page_hero_description' => $state['about_page_hero_description'],
        ]);

        $this->queueSiteRevalidation();

        $this->swalSuccess(
            'About page saved',
            'About content has been updated on the live site.',
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->singletonEditorActionGroup(
                'About / profile',
                fn (): ?Profile => Profile::query()->first(),
                fn (Profile $record): string => $record->name,
                fn (Profile $record) => view('filament.partials.portfolio-record-modal', PortfolioModalRows::profile($record)),
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
}
