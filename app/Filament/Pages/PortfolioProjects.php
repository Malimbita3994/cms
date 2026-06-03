<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesPageAccess;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Support\NavigationGroups;
use App\Filament\Support\PortfolioEditorActions;
use App\Filament\Support\PortfolioFormFields;
use App\Models\PortfolioProject;
use App\Support\LineDelimitedText;
use App\Support\PortfolioAsset;
use App\Support\PortfolioProjectContentItems;
use App\Support\RichContentSanitizer;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
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

class PortfolioProjects extends Page implements HasForms
{
    use AuthorizesPageAccess;
    use InteractsWithForms;
    use InteractsWithPortfolioEditor;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static ?string $navigationLabel = 'Projects';

    protected static ?int $navigationSort = 5;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::CONTENT;

    protected static ?string $slug = 'portfolio-projects';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isDiscovered = false;

    protected string $view = 'filament.pages.portfolio-projects';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $rows = PortfolioProject::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (PortfolioProject $project) => [
                'id' => $project->id,
                'image' => PortfolioAsset::toUploadState($project->image),
                'preview' => PortfolioAsset::toUploadState($project->preview),
                'title' => $project->title,
                'description' => $project->description,
                'technologies' => $project->technologies,
                'achievements' => $project->achievements,
            ])
            ->all();

        $this->form->fill([
            'projects' => PortfolioProjectContentItems::forForm($rows),
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
                        Repeater::make('projects')
                            ->label('Projects')
                            ->schema([
                                Hidden::make('id'),
                                Grid::make()
                                    ->columns(['default' => 1, 'lg' => 2])
                                    ->schema([
                                        Group::make([
                                            $this->portfolioImageUpload(
                                                'image',
                                                'uploads/projects',
                                                'Project image',
                                                160,
                                                'Main image on project cards and detail pages.',
                                            ),
                                            $this->portfolioImageUpload(
                                                'preview',
                                                'uploads/projects/previews',
                                                'Card preview text image',
                                                120,
                                                'Short preview line shown on homepage project cards.',
                                                required: false,
                                            ),
                                        ])
                                            ->extraAttributes(['class' => 'home-editor-col home-editor-col--left']),
                                        Group::make([
                                            TextInput::make('title')
                                                ->label('Title')
                                                ->required(),
                                            PortfolioFormFields::applyRichEditorDefaults(
                                                RichEditor::make('description')
                                                    ->label('Description')
                                                    ->required(),
                                            )
                                                ->columnSpanFull(),
                                            Textarea::make('technologies')
                                                ->label('Technologies')
                                                ->helperText('One item per line.')
                                                ->rows(3)
                                                ->columnSpanFull(),
                                            Textarea::make('achievements')
                                                ->label('Achievements')
                                                ->helperText('One item per line.')
                                                ->rows(3)
                                                ->columnSpanFull(),
                                        ])
                                            ->extraAttributes(['class' => 'home-editor-col home-editor-col--right']),
                                    ]),
                            ])
                            ->extraAttributes(['class' => 'home-editor-repeater'])
                            ->reorderable()
                            ->reorderableWithDragAndDrop()
                            ->itemHeaders(true)
                            ->collapsible(false)
                            ->deletable()
                            ->deleteAction(
                                PortfolioEditorActions::confirmedDelete(
                                    'Remove project?',
                                    'This project will be removed from the homepage and /projects.',
                                ),
                            )
                            ->minItems(1)
                            ->maxItems(24)
                            ->addActionLabel('Add project')
                            ->columnSpanFull()
                            ->helperText('Shown on the homepage #projects section and /projects.'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $this->swalLoading('Saving projects…');

        $rows = $this->form->getState()['projects'] ?? [];
        $keptIds = [];

        foreach ($rows as $index => $row) {
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $image = PortfolioAsset::toPublicPath($row['image'] ?? null) ?? '/projects/dashboard.svg';
            $preview = PortfolioAsset::toPublicPath($row['preview'] ?? null) ?? $image;

            $payload = [
                'sort_order' => $index,
                'title' => $title,
                'description' => RichContentSanitizer::clean($row['description'] ?? '') ?? '',
                'technologies' => LineDelimitedText::toStorage($row['technologies'] ?? null),
                'achievements' => LineDelimitedText::toStorage($row['achievements'] ?? null),
                'image' => $image,
                'preview' => $preview,
            ];

            $id = isset($row['id']) && $row['id'] !== '' && $row['id'] !== null ? (int) $row['id'] : null;

            if ($id && ($project = PortfolioProject::query()->find($id))) {
                $project->update($payload);
                $keptIds[] = $project->id;

                continue;
            }

            $project = PortfolioProject::query()->create($payload);
            $keptIds[] = $project->id;
        }

        if ($keptIds !== []) {
            PortfolioProject::query()->whereNotIn('id', $keptIds)->delete();
        }

        $this->queueSiteRevalidation();

        $this->swalSuccess(
            'Projects saved',
            'Project content has been updated on the live site.',
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
