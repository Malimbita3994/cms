<?php

namespace App\Filament\Resources\Skills\Pages;

use App\Filament\Concerns\ConfiguresStackedPortfolioForm;
use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Concerns\UsesPortfolioRecordFormLayout;
use App\Filament\Resources\Skills\Schemas\AdminSkillForm;
use App\Filament\Resources\Skills\SkillResource;
use App\Filament\Support\PortfolioEditorActions;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

class EditSkill extends EditRecord
{
    use ConfiguresStackedPortfolioForm;
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
        return 'Edit skill';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Update '.$this->getRecord()->name.' for the homepage and /skills.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Skills';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['image'] = PortfolioAsset::toUploadState($data['image'] ?? null);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update([
            'name' => $data['name'],
            'level' => max(0, min(100, (int) ($data['level'] ?? 0))),
            'focus' => RichContentSanitizer::clean($data['focus'] ?? '') ?? '',
            'icon' => $data['icon'],
            'image' => PortfolioAsset::toPublicPath($data['image'] ?? null),
        ]);

        $this->queueSiteRevalidation();

        return $record;
    }

    public function cancel(): void
    {
        $this->redirect(SkillResource::getUrl('index'));
    }

    protected function beforeSave(): void
    {
        $this->swalLoading('Saving skill…');
    }

    protected function afterSave(): void
    {
        $this->swalSuccess('Skill saved', 'Skill content has been updated on the live site.');
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation(false)
                ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                    'Delete this skill?',
                    'This skill will be removed from the homepage and /skills.',
                ))
                ->successNotification(null)
                ->after(fn (): mixed => $this->dispatch(
                    'swal',
                    type: 'success',
                    title: 'Skill deleted',
                    text: 'The skill has been removed.',
                )),
        ];
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
}
