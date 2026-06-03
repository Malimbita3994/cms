<?php

namespace App\Filament\Resources\PortfolioProjects\Pages;

use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Resources\PortfolioProjects\PortfolioProjectResource;
use App\Filament\Resources\PortfolioProjects\Schemas\AdminPortfolioProjectForm;
use App\Filament\Support\PortfolioEditorActions;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class EditPortfolioProject extends EditRecord
{
    use HasPortfolioRecordShell;
    use InteractsWithPortfolioEditor;

    protected static string $resource = PortfolioProjectResource::class;

    protected string $view = 'filament.pages.portfolio-record-form';

    public function form(Schema $schema): Schema
    {
        return AdminPortfolioProjectForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'Edit project';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Update '.$this->getRecord()->title.' for the homepage and /projects.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Projects';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['image'] = PortfolioAsset::toUploadState($data['image'] ?? null);
        $data['preview'] = PortfolioAsset::toUploadState($data['preview'] ?? null);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $image = PortfolioAsset::toPublicPath($data['image'] ?? null) ?? '/projects/dashboard.svg';
        $preview = PortfolioAsset::toPublicPath($data['preview'] ?? null) ?? $image;

        $record->update([
            'title' => $data['title'],
            'description' => RichContentSanitizer::clean($data['description'] ?? '') ?? '',
            'technologies' => $data['technologies'] ?? [],
            'achievements' => $data['achievements'] ?? [],
            'image' => $image,
            'preview' => $preview,
        ]);

        $this->queueSiteRevalidation();

        return $record;
    }

    public function cancel(): void
    {
        $this->redirect(PortfolioProjectResource::getUrl('index'));
    }

    protected function beforeSave(): void
    {
        $this->swalLoading('Saving project…');
    }

    protected function afterSave(): void
    {
        $this->swalSuccess('Project saved', 'Project content has been updated on the live site.');
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
                    'Delete this project?',
                    'This project will be removed from the live site.',
                ))
                ->successNotification(null)
                ->after(fn (): mixed => $this->dispatch(
                    'swal',
                    type: 'success',
                    title: 'Project deleted',
                    text: 'The project has been removed.',
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
