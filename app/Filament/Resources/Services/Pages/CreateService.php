<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Concerns\ConfiguresStackedPortfolioForm;
use App\Filament\Concerns\DelegatesSaveToCreate;
use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Concerns\UsesPortfolioRecordFormLayout;
use App\Filament\Resources\Services\Schemas\AdminServiceForm;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class CreateService extends CreateRecord
{
    use ConfiguresStackedPortfolioForm;
    use DelegatesSaveToCreate;
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
        return 'New service';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Add a service for the homepage #services section and /services page.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Services';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $service = Service::query()->create([
            'sort_order' => (int) Service::query()->max('sort_order') + 1,
            'title' => $data['title'],
            'tagline' => filled($data['tagline'] ?? null) ? trim((string) $data['tagline']) : null,
            'image' => PortfolioAsset::toPublicPath($data['image'] ?? null),
            'description' => RichContentSanitizer::clean($data['description'] ?? '') ?? '',
            'icon' => $data['icon'],
        ]);

        $this->queueSiteRevalidation();

        return $service;
    }

    public function cancel(): void
    {
        $this->redirect(ServiceResource::getUrl('index'));
    }

    protected function beforeCreate(): void
    {
        $this->swalLoading('Creating service…');
    }

    protected function afterCreate(): void
    {
        $this->swalSuccess('Service created', 'The new service is live after revalidation.');
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
        return ServiceResource::getUrl('index');
    }
}
