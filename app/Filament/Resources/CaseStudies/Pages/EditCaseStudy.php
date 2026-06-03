<?php

namespace App\Filament\Resources\CaseStudies\Pages;

use App\Filament\Concerns\ConfiguresStackedPortfolioForm;
use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Concerns\UsesPortfolioRecordFormLayout;
use App\Filament\Resources\CaseStudies\CaseStudyResource;
use App\Filament\Resources\CaseStudies\Schemas\AdminCaseStudyForm;
use App\Filament\Support\PortfolioEditorActions;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class EditCaseStudy extends EditRecord
{
    use ConfiguresStackedPortfolioForm;
    use HasPortfolioRecordShell;
    use InteractsWithPortfolioEditor;
    use UsesPortfolioRecordFormLayout;

    protected static string $resource = CaseStudyResource::class;

    protected string $view = 'filament.pages.portfolio-record-form';

    public function form(Schema $schema): Schema
    {
        return AdminCaseStudyForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'Edit case study';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Update '.$this->getRecord()->title.' for the homepage case studies section.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Case studies';
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
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_published' => (bool) ($data['is_published'] ?? true),
            'title' => $data['title'],
            'image' => PortfolioAsset::toPublicPath($data['image'] ?? null),
            'desc' => RichContentSanitizer::clean($data['desc'] ?? '') ?? '',
            'impact' => RichContentSanitizer::clean($data['impact'] ?? '') ?? '',
            'stack' => $data['stack'] ?? [],
        ]);

        $this->queueSiteRevalidation();

        return $record;
    }

    public function cancel(): void
    {
        $this->redirect(CaseStudyResource::getUrl('index'));
    }

    protected function beforeSave(): void
    {
        $this->swalLoading('Saving case study…');
    }

    protected function afterSave(): void
    {
        $this->swalSuccess('Case study saved', 'Case study content has been updated on the live site.');
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
                    'Delete this case study?',
                    'This case study will be removed from the homepage.',
                ))
                ->successNotification(null)
                ->after(fn (): mixed => $this->dispatch(
                    'swal',
                    type: 'success',
                    title: 'Case study deleted',
                    text: 'The case study has been removed.',
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
