<?php

namespace App\Filament\Resources\Posters\Pages;

use App\Filament\Concerns\InteractsWithPortfolioListStats;
use App\Filament\Resources\Posters\PosterResource;
use App\Models\Poster;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListPosters extends ListRecords
{
    use InteractsWithPortfolioListStats;

    protected static string $resource = PosterResource::class;

    protected string $view = 'filament.pages.list-posters';

    public function mount(): void
    {
        parent::mount();
        $this->loadListStats();
    }

    public function getHeading(): string|Htmlable
    {
        return 'Posters / News';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Manage, publish, feature, and monitor news posters displayed on the public website.';
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            PosterResource::getUrl('index') => 'News & Updates',
            'Posters',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export')
                ->icon(Heroicon::ArrowUpTray)
                ->color('gray')
                ->outlined()
                ->action(fn (): StreamedResponse => $this->exportPostersCsv()),
            CreateAction::make()
                ->label('Create Poster')
                ->icon(Heroicon::Plus),
        ];
    }

    /**
     * @return array<int, array{label: string, value: int|string, hint: string, color: string, icon: string, trend?: string}>
     */
    protected function computeListStats(): array
    {
        $total = Poster::query()->count();
        $published = Poster::query()->where('is_published', true)->count();
        $featured = Poster::query()->where('is_featured', true)->count();
        $updatedWeek = Poster::query()
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();
        $createdThisMonth = Poster::query()
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
        $publishedPct = $total > 0 ? (int) round(($published / $total) * 100) : 0;

        return [
            [
                'label' => 'Total Posts',
                'value' => $total,
                'hint' => $createdThisMonth > 0 ? "+{$createdThisMonth} this month" : 'All posters & news',
                'color' => 'blue',
                'icon' => 'chart',
                'share' => $total > 0 ? 100 : 0,
                'show_share' => false,
            ],
            [
                'label' => 'Published',
                'value' => $published,
                'hint' => 'Currently live',
                'color' => 'green',
                'icon' => 'spark',
                'share' => $publishedPct,
            ],
            [
                'label' => 'Featured Hero',
                'value' => $featured,
                'hint' => 'Homepage spotlight',
                'color' => 'amber',
                'icon' => 'star',
                'share' => $total > 0 ? (int) round(($featured / $total) * 100) : 0,
            ],
            [
                'label' => 'Updated This Week',
                'value' => $updatedWeek,
                'hint' => 'Recent activity',
                'color' => 'purple',
                'icon' => 'clock',
                'share' => $total > 0 ? (int) round(($updatedWeek / $total) * 100) : 0,
            ],
        ];
    }

    private function exportPostersCsv(): StreamedResponse
    {
        $filename = 'posters-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Title', 'Slug', 'Category', 'Status', 'Featured', 'Published', 'Updated']);

            Poster::query()
                ->orderBy('sort_order')
                ->lazy()
                ->each(function (Poster $poster) use ($handle): void {
                    fputcsv($handle, [
                        $poster->title,
                        $poster->slug,
                        $poster->category,
                        $poster->is_published ? 'Published' : 'Draft',
                        $poster->is_featured ? 'Yes' : 'No',
                        $poster->published_at?->toDateTimeString() ?? '',
                        $poster->updated_at?->toDateTimeString() ?? '',
                    ]);
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
