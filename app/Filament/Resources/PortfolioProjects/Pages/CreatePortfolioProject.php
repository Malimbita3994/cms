<?php

namespace App\Filament\Resources\PortfolioProjects\Pages;

use App\Filament\Concerns\DelegatesSaveToCreate;
use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Resources\PortfolioProjects\PortfolioProjectResource;
use App\Filament\Resources\PortfolioProjects\Schemas\AdminPortfolioProjectForm;
use App\Models\PortfolioProject;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class CreatePortfolioProject extends CreateRecord
{
    use DelegatesSaveToCreate;
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
        return 'New project';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Add a portfolio project for the homepage #projects section and /projects.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Projects';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $image = PortfolioAsset::toPublicPath($data['image'] ?? null) ?? '/projects/dashboard.svg';
        $preview = PortfolioAsset::toPublicPath($data['preview'] ?? null) ?? $image;

        $project = PortfolioProject::query()->create([
            'sort_order' => (int) PortfolioProject::query()->max('sort_order') + 1,
            'title' => $data['title'],
            'description' => RichContentSanitizer::clean($data['description'] ?? '') ?? '',
            'technologies' => $data['technologies'] ?? [],
            'achievements' => $data['achievements'] ?? [],
            'image' => $image,
            'preview' => $preview,
        ]);

        $this->queueSiteRevalidation();

        return $project;
    }

    public function cancel(): void
    {
        $this->redirect(PortfolioProjectResource::getUrl('index'));
    }

    protected function beforeCreate(): void
    {
        $this->swalLoading('Creating project…');
    }

    protected function afterCreate(): void
    {
        $this->swalSuccess('Project created', 'The new project is live after revalidation.');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
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

    protected function getRedirectUrl(): string
    {
        return PortfolioProjectResource::getUrl('index');
    }
}
