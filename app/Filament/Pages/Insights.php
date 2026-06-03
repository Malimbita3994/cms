<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesPageAccess;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Support\NavigationGroups;
use App\Filament\Support\PortfolioEditorActions;
use App\Filament\Support\PortfolioFormFields;
use App\Models\Insight;
use App\Support\InsightContentItems;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
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

class Insights extends Page implements HasForms
{
    use AuthorizesPageAccess;
    use InteractsWithForms;
    use InteractsWithPortfolioEditor;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    protected static ?string $navigationLabel = 'Insights';

    protected static ?int $navigationSort = 7;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::CONTENT;

    protected static ?string $slug = 'insights';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isDiscovered = false;

    protected string $view = 'filament.pages.insights';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $rows = Insight::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Insight $insight) => [
                'id' => $insight->id,
                'image' => PortfolioAsset::toUploadState($insight->image),
                'title' => $insight->title,
                'excerpt' => $insight->excerpt,
                'display_date' => $insight->display_date,
            ])
            ->all();

        $this->form->fill([
            'insights' => InsightContentItems::forForm($rows),
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
                        Repeater::make('insights')
                            ->label('Insights')
                            ->schema([
                                Hidden::make('id'),
                                Grid::make()
                                    ->columns(['default' => 1, 'lg' => 2])
                                    ->schema([
                                        Group::make([
                                            $this->portfolioImageUpload(
                                                'image',
                                                'uploads/insights',
                                                'Insight image',
                                                160,
                                                'Shown on insights list and detail cards.',
                                                required: false,
                                            ),
                                        ])
                                            ->extraAttributes(['class' => 'home-editor-col home-editor-col--left']),
                                        Group::make([
                                            TextInput::make('title')
                                                ->label('Title')
                                                ->required(),
                                            TextInput::make('display_date')
                                                ->label('Publication date')
                                                ->placeholder('March 2026')
                                                ->helperText('Displayed on the public insights list.'),
                                            PortfolioFormFields::applyRichEditorDefaults(
                                                RichEditor::make('excerpt')
                                                    ->label('Excerpt')
                                                    ->required(),
                                            )
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
                                    'Remove insight?',
                                    'This insight will be removed from /insights.',
                                ),
                            )
                            ->minItems(1)
                            ->maxItems(48)
                            ->addActionLabel('Add insight')
                            ->columnSpanFull()
                            ->helperText('Shown on /insights and linked from the site navigation.'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $this->swalLoading('Saving insights…');

        $rows = $this->form->getState()['insights'] ?? [];
        $keptIds = [];

        foreach ($rows as $index => $row) {
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $payload = [
                'sort_order' => $index,
                'title' => $title,
                'excerpt' => RichContentSanitizer::clean($row['excerpt'] ?? '') ?? '',
                'display_date' => filled($row['display_date'] ?? null) ? trim((string) $row['display_date']) : null,
                'image' => PortfolioAsset::toPublicPath($row['image'] ?? null) ?? '/insights/government-apis.svg',
            ];

            $id = isset($row['id']) && $row['id'] !== '' && $row['id'] !== null ? (int) $row['id'] : null;

            if ($id && ($insight = Insight::query()->find($id))) {
                $insight->update($payload);
                $keptIds[] = $insight->id;

                continue;
            }

            $insight = Insight::query()->create($payload);
            $keptIds[] = $insight->id;
        }

        if ($keptIds !== []) {
            Insight::query()->whereNotIn('id', $keptIds)->delete();
        }

        $this->queueSiteRevalidation();

        $this->swalSuccess(
            'Insights saved',
            'Insights content has been updated on the live site.',
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
