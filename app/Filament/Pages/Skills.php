<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesPageAccess;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Support\NavigationGroups;
use App\Filament\Support\PortfolioEditorActions;
use App\Filament\Support\PortfolioFormFields;
use App\Models\Skill;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use App\Support\SkillContentItems;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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

class Skills extends Page implements HasForms
{
    use AuthorizesPageAccess;
    use InteractsWithForms;
    use InteractsWithPortfolioEditor;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Skills';

    protected static ?int $navigationSort = 4;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::CONTENT;

    protected static ?string $slug = 'skills';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isDiscovered = false;

    protected string $view = 'filament.pages.skills';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $rows = Skill::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Skill $skill) => [
                'id' => $skill->id,
                'image' => PortfolioAsset::toUploadState($skill->image),
                'name' => $skill->name,
                'level' => $skill->level,
                'focus' => $skill->focus,
                'icon' => $skill->icon,
            ])
            ->all();

        $this->form->fill([
            'skills' => SkillContentItems::forForm($rows),
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
                        Repeater::make('skills')
                            ->label('Skills')
                            ->schema([
                                Hidden::make('id'),
                                Grid::make()
                                    ->columns([
                                        'default' => 1,
                                        'lg' => 2,
                                    ])
                                    ->schema([
                                        Group::make([
                                            $this->portfolioImageUpload(
                                                'image',
                                                'uploads/skills',
                                                'Skill image',
                                                140,
                                                'Optional. Shown on the skill card on the homepage and /skills page.',
                                                required: false,
                                            ),
                                        ])
                                            ->extraAttributes(['class' => 'home-editor-col home-editor-col--left']),
                                        Group::make([
                                            TextInput::make('name')
                                                ->label('Name')
                                                ->required(),
                                            TextInput::make('level')
                                                ->label('Proficiency (%)')
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(100)
                                                ->required()
                                                ->default(80),
                                            Select::make('icon')
                                                ->label('Icon (fallback when no image)')
                                                ->options(SkillContentItems::iconOptions())
                                                ->required()
                                                ->native(false),
                                            PortfolioFormFields::applyRichEditorDefaults(
                                                RichEditor::make('focus')
                                                    ->label('Description')
                                                    ->required(),
                                            )
                                                ->placeholder('Describe this skill…')
                                                ->helperText('Supports basic formatting (bold, lists, links).')
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
                                    'Remove skill?',
                                    'This skill will be removed from the homepage and /skills.',
                                ),
                            )
                            ->minItems(1)
                            ->maxItems(24)
                            ->addActionLabel('Add skill')
                            ->columnSpanFull()
                            ->helperText('Manage all skills shown in the homepage #skills section and on /skills.'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $this->swalLoading('Saving skills…');

        $state = $this->form->getState();
        $rows = $state['skills'] ?? [];
        $keptIds = [];

        foreach ($rows as $index => $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $payload = [
                'sort_order' => $index,
                'name' => $name,
                'level' => max(0, min(100, (int) ($row['level'] ?? 0))),
                'focus' => RichContentSanitizer::clean($row['focus'] ?? '') ?? '',
                'icon' => trim((string) ($row['icon'] ?? 'ClipboardDocumentList')),
                'image' => PortfolioAsset::toPublicPath($row['image'] ?? null),
            ];

            $id = isset($row['id']) && $row['id'] !== '' && $row['id'] !== null
                ? (int) $row['id']
                : null;

            if ($id) {
                $skill = Skill::query()->find($id);
                if ($skill) {
                    $skill->update($payload);
                    $keptIds[] = $skill->id;

                    continue;
                }
            }

            $skill = Skill::query()->create($payload);
            $keptIds[] = $skill->id;
        }

        if ($keptIds !== []) {
            Skill::query()->whereNotIn('id', $keptIds)->delete();
        }

        $this->queueSiteRevalidation();

        $this->swalSuccess(
            'Skills saved',
            'Skills content has been updated on the live site.',
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
