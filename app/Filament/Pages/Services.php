<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesPageAccess;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Support\NavigationGroups;
use App\Filament\Support\PortfolioEditorActions;
use App\Filament\Support\PortfolioFormFields;
use App\Models\Service;
use App\Support\RichContentSanitizer;
use App\Support\ServiceContentItems;
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
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;

class Services extends Page implements HasForms
{
    use AuthorizesPageAccess;
    use InteractsWithForms;
    use InteractsWithPortfolioEditor;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $navigationLabel = 'Services';

    protected static ?int $navigationSort = 6;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::CONTENT;

    protected static ?string $slug = 'services';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isDiscovered = false;

    protected string $view = 'filament.pages.services';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $rows = Service::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Service $service) => [
                'id' => $service->id,
                'title' => $service->title,
                'description' => $service->description,
                'icon' => $service->icon,
            ])
            ->all();

        $this->form->fill([
            'services' => ServiceContentItems::forForm($rows),
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
                        Repeater::make('services')
                            ->label('Services')
                            ->schema([
                                Hidden::make('id'),
                                TextInput::make('title')
                                    ->label('Title')
                                    ->required(),
                                Select::make('icon')
                                    ->label('Icon')
                                    ->options(ServiceContentItems::iconOptions())
                                    ->required()
                                    ->native(false),
                                PortfolioFormFields::applyRichEditorDefaults(
                                    RichEditor::make('description')
                                        ->label('Description')
                                        ->required(),
                                )
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->extraAttributes(['class' => 'home-editor-repeater'])
                            ->reorderable()
                            ->reorderableWithDragAndDrop()
                            ->itemHeaders(true)
                            ->collapsible(false)
                            ->deletable()
                            ->deleteAction(
                                PortfolioEditorActions::confirmedDelete(
                                    'Remove service?',
                                    'This service will be removed from the homepage and /services.',
                                ),
                            )
                            ->minItems(1)
                            ->maxItems(24)
                            ->addActionLabel('Add service')
                            ->columnSpanFull()
                            ->helperText('Shown on the homepage #services section and /services.'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $this->swalLoading('Saving services…');

        $rows = $this->form->getState()['services'] ?? [];
        $keptIds = [];

        foreach ($rows as $index => $row) {
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $payload = [
                'sort_order' => $index,
                'title' => $title,
                'description' => RichContentSanitizer::clean($row['description'] ?? '') ?? '',
                'icon' => trim((string) ($row['icon'] ?? 'CommandLine')),
            ];

            $id = isset($row['id']) && $row['id'] !== '' && $row['id'] !== null ? (int) $row['id'] : null;

            if ($id && ($service = Service::query()->find($id))) {
                $service->update($payload);
                $keptIds[] = $service->id;

                continue;
            }

            $service = Service::query()->create($payload);
            $keptIds[] = $service->id;
        }

        if ($keptIds !== []) {
            Service::query()->whereNotIn('id', $keptIds)->delete();
        }

        $this->queueSiteRevalidation();

        $this->swalSuccess(
            'Services saved',
            'Services content has been updated on the live site.',
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
