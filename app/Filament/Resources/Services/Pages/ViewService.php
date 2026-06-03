<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\HasPortfolioRecordViewUrls;
use App\Filament\Resources\Services\Schemas\AdminServiceForm;
use App\Filament\Resources\Services\ServiceResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;

class ViewService extends ViewRecord
{
    use HasPortfolioRecordShell;
    use HasPortfolioRecordViewUrls;

    protected static string $resource = ServiceResource::class;

    protected string $view = 'filament.pages.portfolio-record-view';

    public function form(Schema $schema): Schema
    {
        return AdminServiceForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'View service';
    }

    public function getPortfolioFormLead(): string
    {
        return $this->getRecord()->title;
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Services';
    }

    public function getPortfolioEditUrl(): string
    {
        return ServiceResource::getUrl('edit', ['record' => $this->getRecord()]);
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
