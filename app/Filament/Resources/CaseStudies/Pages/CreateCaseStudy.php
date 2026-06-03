<?php

namespace App\Filament\Resources\CaseStudies\Pages;

use App\Filament\Concerns\ConfiguresStackedPortfolioForm;
use App\Filament\Concerns\DelegatesSaveToCreate;
use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Concerns\UsesPortfolioRecordFormLayout;
use App\Filament\Resources\CaseStudies\CaseStudyResource;
use App\Filament\Resources\CaseStudies\Schemas\AdminCaseStudyForm;
use App\Models\CaseStudy;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class CreateCaseStudy extends CreateRecord
{
    use ConfiguresStackedPortfolioForm;
    use DelegatesSaveToCreate;
    use HasPortfolioRecordShell;
    use InteractsWithPortfolioEditor;
    use UsesPortfolioRecordFormLayout;

    protected static string $resource = CaseStudyResource::class;

    protected string $view = 'filament.pages.portfolio-record-form';

    public function form(Schema $schema): Schema
    {
        return AdminCaseStudyForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'New case study';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Add a project case study for the homepage case studies section.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Case studies';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $caseStudy = CaseStudy::query()->create([
            'sort_order' => (int) ($data['sort_order'] ?? ((int) CaseStudy::query()->max('sort_order') + 1)),
            'is_published' => (bool) ($data['is_published'] ?? true),
            'title' => $data['title'],
            'image' => PortfolioAsset::toPublicPath($data['image'] ?? null),
            'desc' => RichContentSanitizer::clean($data['desc'] ?? '') ?? '',
            'impact' => RichContentSanitizer::clean($data['impact'] ?? '') ?? '',
            'stack' => $data['stack'] ?? [],
        ]);

        $this->queueSiteRevalidation();

        return $caseStudy;
    }

    public function cancel(): void
    {
        $this->redirect(CaseStudyResource::getUrl('index'));
    }

    protected function beforeCreate(): void
    {
        $this->swalLoading('Creating case study…');
    }

    protected function afterCreate(): void
    {
        $this->swalSuccess('Case study created', 'The new case study is live after revalidation.');
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
        return CaseStudyResource::getUrl('index');
    }
}
