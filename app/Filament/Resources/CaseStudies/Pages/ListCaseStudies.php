<?php

namespace App\Filament\Resources\CaseStudies\Pages;

use App\Filament\Concerns\InteractsWithPortfolioListStats;
use App\Filament\Resources\CaseStudies\CaseStudyResource;
use App\Models\CaseStudy;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCaseStudies extends ListRecords
{
    use InteractsWithPortfolioListStats;

    protected static string $resource = CaseStudyResource::class;

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
        $total = CaseStudy::query()->count();
        $withStack = CaseStudy::query()
            ->get()
            ->filter(fn (CaseStudy $study): bool => count($study->stack ?? []) > 0)
            ->count();
        $updatedWeek = CaseStudy::query()
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        return [
            [
                'label' => 'Total case studies',
                'value' => $total,
                'hint' => 'Shown on the home page',
                'color' => 'orange',
                'icon' => 'document',
            ],
            [
                'label' => 'With tech stack',
                'value' => $withStack,
                'hint' => 'Stack tags listed',
                'color' => 'blue',
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
