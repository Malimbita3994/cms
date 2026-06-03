<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Concerns\ConfiguresStackedPortfolioForm;
use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Concerns\UsesPortfolioRecordFormLayout;
use App\Filament\Resources\Services\Schemas\AdminServiceForm;
use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Support\PortfolioEditorActions;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class EditService extends EditRecord
{
    use ConfiguresStackedPortfolioForm;
    use HasPortfolioRecordShell;
    use InteractsWithPortfolioEditor;
    use UsesPortfolioRecordFormLayout;

    protected static string $resource = ServiceResource::class;

    protected string $view = 'filament.pages.portfolio-record-form';

    public function form(Schema $schema): Schema
    {
        return AdminServiceForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'Edit service';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Update '.$this->getRecord()->title.' for the homepage and /services.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Services';
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
            'tagline' => filled($data['tagline'] ?? null) ? trim((string) $data['tagline']) : null,
            'image' => PortfolioAsset::toPublicPath($data['image'] ?? null),
            'description' => RichContentSanitizer::clean($data['description'] ?? '') ?? '',
            'icon' => $data['icon'],
        ]);

        $this->queueSiteRevalidation();

        return $record;
    }

    public function cancel(): void
    {
        $this->redirect(ServiceResource::getUrl('index'));
    }

    protected function beforeSave(): void
    {
        $this->swalLoading('Saving service…');
    }

    protected function afterSave(): void
    {
        $this->swalSuccess('Service saved', 'Service content has been updated on the live site.');
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
                    'Delete this service?',
                    'This service will be removed from the live site.',
                ))
                ->successNotification(null)
                ->after(fn (): mixed => $this->dispatch(
                    'swal',
                    type: 'success',
                    title: 'Service deleted',
                    text: 'The service has been removed.',
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
