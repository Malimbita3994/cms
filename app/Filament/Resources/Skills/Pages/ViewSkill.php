<?php

namespace App\Filament\Resources\Skills\Pages;

use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\HasPortfolioRecordViewUrls;
use App\Filament\Resources\Skills\Schemas\AdminSkillForm;
use App\Filament\Resources\Skills\SkillResource;
use App\Support\PortfolioAsset;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;

class ViewSkill extends ViewRecord
{
    use HasPortfolioRecordShell;
    use HasPortfolioRecordViewUrls;

    protected static string $resource = SkillResource::class;

    protected string $view = 'filament.pages.portfolio-record-view';

    public function form(Schema $schema): Schema
    {
        return AdminSkillForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'View skill';
    }

    public function getPortfolioFormLead(): string
    {
        return $this->getRecord()->name;
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Skills';
    }

    public function getPortfolioEditUrl(): string
    {
        return SkillResource::getUrl('edit', ['record' => $this->getRecord()]);
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
