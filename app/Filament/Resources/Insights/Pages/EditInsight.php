<?php

namespace App\Filament\Resources\Insights\Pages;

use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Resources\Insights\InsightResource;
use App\Filament\Resources\Insights\Schemas\AdminInsightForm;
use App\Filament\Support\PortfolioEditorActions;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class EditInsight extends EditRecord
{
    use HasPortfolioRecordShell;
    use InteractsWithPortfolioEditor;

    protected static string $resource = InsightResource::class;

    protected string $view = 'filament.pages.portfolio-record-form';

    public function form(Schema $schema): Schema
    {
        return AdminInsightForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'Edit insight';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Update '.$this->getRecord()->title.' for /insights.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Insights';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['image'] = PortfolioAsset::toUploadState($data['image'] ?? null);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update([
            'title' => $data['title'],
            'excerpt' => RichContentSanitizer::clean($data['excerpt'] ?? '') ?? '',
            'display_date' => filled($data['display_date'] ?? null) ? trim((string) $data['display_date']) : null,
            'image' => PortfolioAsset::toPublicPath($data['image'] ?? null) ?? '/insights/government-apis.svg',
        ]);

        $this->queueSiteRevalidation();

        return $record;
    }

    public function cancel(): void
    {
        $this->redirect(InsightResource::getUrl('index'));
    }

    protected function beforeSave(): void
    {
        $this->swalLoading('Saving insight…');
    }

    protected function afterSave(): void
    {
        $this->swalSuccess('Insight saved', 'Insight content has been updated on the live site.');
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation(false)
                ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                    'Delete this insight?',
                    'This insight will be removed from /insights.',
                ))
                ->successNotification(null)
                ->after(fn (): mixed => $this->dispatch(
                    'swal',
                    type: 'success',
                    title: 'Insight deleted',
                    text: 'The insight has been removed.',
                )),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
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
