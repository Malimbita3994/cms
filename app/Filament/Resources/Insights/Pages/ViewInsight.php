<?php

namespace App\Filament\Resources\Insights\Pages;

use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\HasPortfolioRecordViewUrls;
use App\Filament\Resources\Insights\InsightResource;
use App\Filament\Resources\Insights\Schemas\AdminInsightForm;
use App\Support\PortfolioAsset;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;

class ViewInsight extends ViewRecord
{
    use HasPortfolioRecordShell;
    use HasPortfolioRecordViewUrls;

    protected static string $resource = InsightResource::class;

    protected string $view = 'filament.pages.portfolio-record-view';

    public function form(Schema $schema): Schema
    {
        return AdminInsightForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'View insight';
    }

    public function getPortfolioFormLead(): string
    {
        return $this->getRecord()->title;
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Insights';
    }

    public function getPortfolioEditUrl(): string
    {
        return InsightResource::getUrl('edit', ['record' => $this->getRecord()]);
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
