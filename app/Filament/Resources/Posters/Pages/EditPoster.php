<?php

namespace App\Filament\Resources\Posters\Pages;

use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Resources\Posters\PosterResource;
use App\Filament\Resources\Posters\Schemas\AdminPosterForm;
use App\Filament\Support\PortfolioEditorActions;
use App\Models\Poster;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EditPoster extends EditRecord
{
    use HasPortfolioRecordShell;
    use InteractsWithPortfolioEditor;

    protected static string $resource = PosterResource::class;

    protected string $view = 'filament.pages.portfolio-record-form';

    public function form(Schema $schema): Schema
    {
        return AdminPosterForm::configure($schema);
    }

    public function getPortfolioFormTitle(): string
    {
        return 'Edit poster / news update';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Update '.$this->getRecord()->title.' for the public homepage.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Posters / News';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['image'] = PortfolioAsset::toUploadState($data['image'] ?? null);
        $data['pdf'] = PortfolioAsset::toUploadState($data['pdf'] ?? null);
        $data['title_for_slug'] = $data['title'] ?? '';

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Poster $record */
        $wasPublished = (bool) $record->is_published;
        $isPublished = (bool) ($data['is_published'] ?? false);
        $title = trim((string) ($data['title'] ?? ''));
        $slug = filled($data['slug'] ?? null)
            ? trim((string) $data['slug'])
            : Poster::uniqueSlug($title, $record->id);

        $record->update([
            'is_published' => $isPublished,
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'title' => $title,
            'slug' => $slug,
            'category' => trim((string) ($data['category'] ?? 'General')),
            'short_description' => RichContentSanitizer::clean($data['short_description'] ?? '') ?? '',
            'content' => RichContentSanitizer::clean($data['content'] ?? '') ?? '',
            'image' => PortfolioAsset::toPublicPath($data['image'] ?? null),
            'pdf' => PortfolioAsset::toPublicPath($data['pdf'] ?? null),
            'published_at' => $isPublished
                ? ($record->published_at ?? now())
                : ($wasPublished ? null : $record->published_at),
            'updated_by' => Auth::id(),
        ]);

        $this->queueSiteRevalidation();

        return $record;
    }

    public function cancel(): void
    {
        $this->redirect(PosterResource::getUrl('index'));
    }

    protected function beforeSave(): void
    {
        $this->swalLoading('Saving poster…');
    }

    protected function afterSave(): void
    {
        $this->swalSuccess('Poster saved', 'Homepage updates have been refreshed.');
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
                    'Delete this poster?',
                    'This poster will be removed from the public homepage.',
                ))
                ->successNotification(null)
                ->after(fn (): mixed => $this->dispatch(
                    'swal',
                    type: 'success',
                    title: 'Poster deleted',
                    text: 'The poster has been removed.',
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
