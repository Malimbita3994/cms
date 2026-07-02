<?php

namespace App\Filament\Resources\Posters\Pages;

use App\Filament\Concerns\InteractsWithPortfolioListStats;
use App\Filament\Resources\Posters\PosterResource;
use App\Models\Poster;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPosters extends ListRecords
{
    use InteractsWithPortfolioListStats;

    protected static string $resource = PosterResource::class;

    protected string $view = 'filament.pages.list-records-with-stats';

    public function mount(): void
    {
        parent::mount();
        $this->loadListStats();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * @return array<int, array{label: string, value: int|string, hint: string, color: string, icon: string}>
     */
    protected function computeListStats(): array
    {
        $total = Poster::query()->count();
        $published = Poster::query()->where('is_published', true)->count();
        $featured = Poster::query()->where('is_featured', true)->count();
        $updatedWeek = Poster::query()
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        return [
            [
                'label' => 'Total posters',
                'value' => $total,
                'hint' => 'All updates',
                'color' => 'orange',
                'icon' => 'chart',
            ],
            [
                'label' => 'Published',
                'value' => $published,
                'hint' => 'Live on site',
                'color' => 'green',
                'icon' => 'spark',
            ],
            [
                'label' => 'Featured',
                'value' => $featured,
                'hint' => 'Homepage hero',
                'color' => 'amber',
                'icon' => 'image',
            ],
            [
                'label' => 'Updated (7d)',
                'value' => $updatedWeek,
                'hint' => 'Recently edited',
                'color' => 'blue',
                'icon' => 'chart',
            ],
        ];
    }
}
