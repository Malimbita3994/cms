<?php

namespace App\Filament\Resources\CareerTimelineEntries\Pages;

use App\Filament\Concerns\InteractsWithPortfolioListStats;
use App\Filament\Resources\CareerTimelineEntries\CareerTimelineEntryResource;
use App\Models\CareerTimelineEntry;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCareerTimelineEntries extends ListRecords
{
    use InteractsWithPortfolioListStats;

    protected static string $resource = CareerTimelineEntryResource::class;

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
        $total = CareerTimelineEntry::query()->count();
        $updatedWeek = CareerTimelineEntry::query()
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        return [
            [
                'label' => 'Timeline entries',
                'value' => $total,
                'hint' => 'Homepage career journey',
                'color' => 'orange',
                'icon' => 'chart',
            ],
            [
                'label' => 'Updated (7d)',
                'value' => $updatedWeek,
                'hint' => 'Recently edited',
                'color' => 'green',
                'icon' => 'spark',
            ],
        ];
    }
}
