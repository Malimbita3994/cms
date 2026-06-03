<?php

namespace App\Filament\Resources\Skills\Pages;

use App\Filament\Concerns\ConfiguresStackedPortfolioForm;
use App\Filament\Concerns\DelegatesSaveToCreate;
use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Concerns\UsesPortfolioRecordFormLayout;
use App\Filament\Resources\Skills\Schemas\AdminSkillForm;
use App\Filament\Resources\Skills\SkillResource;
use App\Models\Skill;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class CreateSkill extends CreateRecord
{
    use ConfiguresStackedPortfolioForm;
    use DelegatesSaveToCreate;
    use HasPortfolioRecordShell;
    use InteractsWithPortfolioEditor;
    use UsesPortfolioRecordFormLayout;

    protected static string $resource = SkillResource::class;

    protected string $view = 'filament.pages.portfolio-record-form';

    public function form(Schema $schema): Schema
    {
        return AdminSkillForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'New skill';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Add a skill for the homepage #skills section and /skills pages.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Skills';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $skill = Skill::query()->create([
            'sort_order' => (int) Skill::query()->max('sort_order') + 1,
            'name' => $data['name'],
            'level' => max(0, min(100, (int) ($data['level'] ?? 0))),
            'focus' => RichContentSanitizer::clean($data['focus'] ?? '') ?? '',
            'icon' => $data['icon'],
            'image' => PortfolioAsset::toPublicPath($data['image'] ?? null),
        ]);

        $this->queueSiteRevalidation();

        return $skill;
    }

    public function cancel(): void
    {
        $this->redirect(SkillResource::getUrl('index'));
    }

    protected function beforeCreate(): void
    {
        $this->swalLoading('Creating skill…');
    }

    protected function afterCreate(): void
    {
        $this->swalSuccess('Skill created', 'The new skill is live after revalidation.');
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
        return SkillResource::getUrl('index');
    }
}
