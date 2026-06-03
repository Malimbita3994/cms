<?php

namespace App\Filament\Resources\CareerTimelineEntries\Pages;

use App\Filament\Concerns\ConfiguresStackedPortfolioForm;
use App\Filament\Concerns\DelegatesSaveToCreate;
use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Concerns\UsesPortfolioRecordFormLayout;
use App\Filament\Resources\CareerTimelineEntries\CareerTimelineEntryResource;
use App\Filament\Resources\CareerTimelineEntries\Schemas\AdminCareerTimelineEntryForm;
use App\Models\CareerTimelineEntry;
use App\Support\RichContentSanitizer;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class CreateCareerTimelineEntry extends CreateRecord
{
    use ConfiguresStackedPortfolioForm;
    use DelegatesSaveToCreate;
    use HasPortfolioRecordShell;
    use InteractsWithPortfolioEditor;
    use UsesPortfolioRecordFormLayout;

    protected static string $resource = CareerTimelineEntryResource::class;

    protected string $view = 'filament.pages.portfolio-record-form';

    public function form(Schema $schema): Schema
    {
        return AdminCareerTimelineEntryForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'New career journey entry';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Add a role or milestone for the homepage career timeline.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Career Journey';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $entry = CareerTimelineEntry::query()->create([
            'sort_order' => (int) ($data['sort_order'] ?? ((int) CareerTimelineEntry::query()->max('sort_order') + 1)),
            'period_label' => $data['period_label'],
            'title' => $data['title'],
            'description' => RichContentSanitizer::clean($data['description'] ?? '') ?? '',
        ]);

        $this->queueSiteRevalidation();

        return $entry;
    }

    public function cancel(): void
    {
        $this->redirect(CareerTimelineEntryResource::getUrl('index'));
    }

    protected function beforeCreate(): void
    {
        $this->swalLoading('Creating career entry…');
    }

    protected function afterCreate(): void
    {
        $this->swalSuccess(
            'Career entry created',
            'The new entry is live after revalidation.',
        );
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
        return CareerTimelineEntryResource::getUrl('index');
    }
}
