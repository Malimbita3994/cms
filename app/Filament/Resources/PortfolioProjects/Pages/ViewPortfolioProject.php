<?php

namespace App\Filament\Resources\PortfolioProjects\Pages;

use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\HasPortfolioRecordViewUrls;
use App\Filament\Resources\PortfolioProjects\PortfolioProjectResource;
use App\Filament\Resources\PortfolioProjects\Schemas\AdminPortfolioProjectForm;
use App\Support\PortfolioAsset;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;

class ViewPortfolioProject extends ViewRecord
{
    use HasPortfolioRecordShell;
    use HasPortfolioRecordViewUrls;

    protected static string $resource = PortfolioProjectResource::class;

    protected string $view = 'filament.pages.portfolio-record-view';

    public function form(Schema $schema): Schema
    {
        return AdminPortfolioProjectForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'View project';
    }

    public function getPortfolioFormLead(): string
    {
        return $this->getRecord()->title;
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Projects';
    }

    public function getPortfolioEditUrl(): string
    {
        return PortfolioProjectResource::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['image'] = PortfolioAsset::toUploadState($data['image'] ?? null);
        $data['preview'] = PortfolioAsset::toUploadState($data['preview'] ?? null);

        return $data;
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
