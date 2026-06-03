<?php

namespace App\Filament\Resources\Insights\Pages;

use App\Filament\Concerns\DelegatesSaveToCreate;
use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Resources\Insights\InsightResource;
use App\Filament\Resources\Insights\Schemas\AdminInsightForm;
use App\Models\Insight;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class CreateInsight extends CreateRecord
{
    use DelegatesSaveToCreate;
    use HasPortfolioRecordShell;
    use InteractsWithPortfolioEditor;

    protected static string $resource = InsightResource::class;

    protected string $view = 'filament.pages.portfolio-record-form';

    public function form(Schema $schema): Schema
    {
        return AdminInsightForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'New insight';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Add an insight for /insights and site navigation.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Insights';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $insight = Insight::query()->create([
            'sort_order' => (int) Insight::query()->max('sort_order') + 1,
            'title' => $data['title'],
            'excerpt' => RichContentSanitizer::clean($data['excerpt'] ?? '') ?? '',
            'display_date' => filled($data['display_date'] ?? null) ? trim((string) $data['display_date']) : null,
            'image' => PortfolioAsset::toPublicPath($data['image'] ?? null) ?? '/insights/government-apis.svg',
        ]);

        $this->queueSiteRevalidation();

        return $insight;
    }

    public function cancel(): void
    {
        $this->redirect(InsightResource::getUrl('index'));
    }

    protected function beforeCreate(): void
    {
        $this->swalLoading('Creating insight…');
    }

    protected function afterCreate(): void
    {
        $this->swalSuccess('Insight created', 'The new insight is live after revalidation.');
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
        return InsightResource::getUrl('index');
    }
}
