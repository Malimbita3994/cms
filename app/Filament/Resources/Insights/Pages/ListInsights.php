<?php

namespace App\Filament\Resources\Insights\Pages;

use App\Filament\Concerns\InteractsWithPortfolioListStats;
use App\Filament\Resources\Insights\InsightResource;
use App\Models\Insight;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInsights extends ListRecords
{
    use InteractsWithPortfolioListStats;

    protected static string $resource = InsightResource::class;

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
        $total = Insight::query()->count();
        $withImage = Insight::query()
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->count();
        $withDate = Insight::query()
            ->whereNotNull('display_date')
            ->where('display_date', '!=', '')
            ->count();
        $updatedWeek = Insight::query()
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        return [
            [
                'label' => 'Total insights',
                'value' => $total,
                'hint' => 'Published notes',
                'color' => 'orange',
                'icon' => 'chart',
            ],
            [
                'label' => 'With images',
                'value' => $withImage,
                'hint' => 'Custom artwork',
                'color' => 'blue',
                'icon' => 'image',
            ],
            [
                'label' => 'With dates',
                'value' => $withDate,
                'hint' => 'Publication labels',
                'color' => 'green',
                'icon' => 'spark',
            ],
            [
                'label' => 'Updated (7d)',
                'value' => $updatedWeek,
                'hint' => 'Recently edited',
                'color' => 'amber',
                'icon' => 'chart',
            ],
        ];
    }
}
