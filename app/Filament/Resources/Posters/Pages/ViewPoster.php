<?php

namespace App\Filament\Resources\Posters\Pages;

use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\HasPortfolioRecordViewUrls;
use App\Filament\Resources\Posters\PosterResource;
use App\Filament\Resources\Posters\Schemas\AdminPosterForm;
use App\Support\PortfolioAsset;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;

class ViewPoster extends ViewRecord
{
    use HasPortfolioRecordShell;
    use HasPortfolioRecordViewUrls;

    protected static string $resource = PosterResource::class;

    protected string $view = 'filament.pages.portfolio-record-view';

    public function form(Schema $schema): Schema
    {
        return AdminPosterForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'View poster';
    }

    public function getPortfolioFormLead(): string
    {
        return $this->getRecord()->title;
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Posters / News';
    }

    public function getPortfolioEditUrl(): string
    {
        return PosterResource::getUrl('edit', ['record' => $this->getRecord()]);
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
