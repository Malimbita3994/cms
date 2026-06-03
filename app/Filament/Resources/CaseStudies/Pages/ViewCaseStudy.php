<?php

namespace App\Filament\Resources\CaseStudies\Pages;

use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\HasPortfolioRecordViewUrls;
use App\Filament\Resources\CaseStudies\CaseStudyResource;
use App\Filament\Resources\CaseStudies\Schemas\AdminCaseStudyForm;
use App\Support\PortfolioAsset;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;

class ViewCaseStudy extends ViewRecord
{
    use HasPortfolioRecordShell;
    use HasPortfolioRecordViewUrls;

    protected static string $resource = CaseStudyResource::class;

    protected string $view = 'filament.pages.portfolio-record-view';

    public function form(Schema $schema): Schema
    {
        return AdminCaseStudyForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'View case study';
    }

    public function getPortfolioFormLead(): string
    {
        return $this->getRecord()->title;
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Case studies';
    }

    public function getPortfolioEditUrl(): string
    {
        return CaseStudyResource::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['image'] = PortfolioAsset::toUploadState($data['image'] ?? null);

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
