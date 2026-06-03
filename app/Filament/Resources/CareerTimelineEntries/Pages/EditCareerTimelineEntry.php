<?php

namespace App\Filament\Resources\CareerTimelineEntries\Pages;

use App\Filament\Concerns\ConfiguresStackedPortfolioForm;
use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Concerns\UsesPortfolioRecordFormLayout;
use App\Filament\Resources\CareerTimelineEntries\CareerTimelineEntryResource;
use App\Filament\Resources\CareerTimelineEntries\Schemas\AdminCareerTimelineEntryForm;
use App\Filament\Support\PortfolioEditorActions;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use App\Support\RichContentSanitizer;
use Illuminate\Database\Eloquent\Model;

class EditCareerTimelineEntry extends EditRecord
{
    use ConfiguresStackedPortfolioForm;
    use HasPortfolioRecordShell;
    use InteractsWithPortfolioEditor;
    use UsesPortfolioRecordFormLayout;

    protected static string $resource = CareerTimelineEntryResource::class;

    protected string $view = 'filament.pages.portfolio-record-form';

    public function form(Schema $schema): Schema
    {
        return AdminCareerTimelineEntryForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'Edit career journey entry';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Update '.$this->getRecord()->title.' for the homepage career timeline.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Career Journey';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update([
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'period_label' => $data['period_label'],
            'title' => $data['title'],
            'description' => RichContentSanitizer::clean($data['description'] ?? '') ?? '',
        ]);

        $this->queueSiteRevalidation();

        return $record;
    }

    public function cancel(): void
    {
        $this->redirect(CareerTimelineEntryResource::getUrl('index'));
    }

    protected function beforeSave(): void
    {
        $this->swalLoading('Saving career entry…');
    }

    protected function afterSave(): void
    {
        $this->swalSuccess(
            'Career entry saved',
            'Career journey content has been updated on the live site.',
        );
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
                    'Delete this career entry?',
                    'It will be removed from the Career Journey section on your site.',
                ))
                ->successNotification(null)
                ->after(fn (): mixed => $this->dispatch(
                    'swal',
                    type: 'success',
                    title: 'Career entry deleted',
                    text: 'The entry has been removed.',
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
