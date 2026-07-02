<?php

namespace App\Filament\Resources\Posters\Pages;

use App\Filament\Concerns\DelegatesSaveToCreate;
use App\Filament\Concerns\HasPortfolioRecordShell;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Resources\Posters\PosterResource;
use App\Filament\Resources\Posters\Schemas\AdminPosterForm;
use App\Models\Poster;
use App\Support\PortfolioAsset;
use App\Support\RichContentSanitizer;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreatePoster extends CreateRecord
{
    use DelegatesSaveToCreate;
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
        return 'New poster / news update';
    }

    public function getPortfolioFormLead(): string
    {
        return 'Create homepage news, announcements, blogs, or entertainment posts.';
    }

    public function getPortfolioFormBreadcrumb(): string
    {
        return 'Posters / News';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $isPublished = (bool) ($data['is_published'] ?? false);
        $title = trim((string) ($data['title'] ?? ''));
        $slug = filled($data['slug'] ?? null)
            ? trim((string) $data['slug'])
            : Poster::uniqueSlug($title);

        $poster = Poster::query()->create([
            'sort_order' => (int) Poster::query()->max('sort_order') + 1,
            'is_published' => $isPublished,
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'title' => $title,
            'slug' => $slug,
            'category' => trim((string) ($data['category'] ?? 'General')),
            'short_description' => RichContentSanitizer::clean($data['short_description'] ?? '') ?? '',
            'content' => RichContentSanitizer::clean($data['content'] ?? '') ?? '',
            'image' => PortfolioAsset::toPublicPath($data['image'] ?? null),
            'pdf' => PortfolioAsset::toPublicPath($data['pdf'] ?? null),
            'published_at' => $isPublished ? now() : null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        $this->queueSiteRevalidation();

        return $poster;
    }

    public function cancel(): void
    {
        $this->redirect(PosterResource::getUrl('index'));
    }

    protected function beforeCreate(): void
    {
        $this->swalLoading('Creating poster…');
    }

    protected function afterCreate(): void
    {
        $this->swalSuccess('Poster created', 'The new update is live after revalidation.');
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
        return PosterResource::getUrl('index');
    }
}
