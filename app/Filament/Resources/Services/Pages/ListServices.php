<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Concerns\InteractsWithPortfolioListStats;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServices extends ListRecords
{
    use InteractsWithPortfolioListStats;

    protected static string $resource = ServiceResource::class;

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
        $total = Service::query()->count();
        $updatedWeek = Service::query()
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();
        $withDescription = Service::query()
            ->whereNotNull('description')
            ->where('description', '!=', '')
            ->count();
        $maxOrder = (int) Service::query()->max('sort_order');

        return [
            [
                'label' => 'Total services',
                'value' => $total,
                'hint' => 'Published offerings',
                'color' => 'blue',
                'icon' => 'wrench',
            ],
            [
                'label' => 'Updated (7d)',
                'value' => $updatedWeek,
                'hint' => 'Recently edited',
                'color' => 'green',
                'icon' => 'spark',
            ],
            [
                'label' => 'With content',
                'value' => $withDescription,
                'hint' => 'Has descriptions',
                'color' => 'orange',
                'icon' => 'chart',
            ],
            [
                'label' => 'Display slots',
                'value' => $maxOrder + 1,
                'hint' => 'Sort order capacity',
                'color' => 'amber',
                'icon' => 'chart',
            ],
        ];
    }
}
