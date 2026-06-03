<?php

namespace App\Filament\Resources\PortfolioProjects\Pages;

use App\Filament\Concerns\InteractsWithPortfolioListStats;
use App\Filament\Resources\PortfolioProjects\PortfolioProjectResource;
use App\Models\PortfolioProject;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPortfolioProjects extends ListRecords
{
    use InteractsWithPortfolioListStats;

    protected static string $resource = PortfolioProjectResource::class;

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
        $total = PortfolioProject::query()->count();
        $withPreview = PortfolioProject::query()
            ->whereNotNull('preview')
            ->where('preview', '!=', '')
            ->count();
        $withTech = PortfolioProject::query()
            ->get()
            ->filter(fn (PortfolioProject $project): bool => count($project->technologies ?? []) > 0)
            ->count();
        $updatedWeek = PortfolioProject::query()
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        return [
            [
                'label' => 'Total projects',
                'value' => $total,
                'hint' => 'Portfolio entries',
                'color' => 'orange',
                'icon' => 'folder',
            ],
            [
                'label' => 'With preview',
                'value' => $withPreview,
                'hint' => 'Homepage card previews',
                'color' => 'blue',
                'icon' => 'image',
            ],
            [
                'label' => 'With tech stack',
                'value' => $withTech,
                'hint' => 'Technologies listed',
                'color' => 'green',
                'icon' => 'chart',
            ],
            [
                'label' => 'Updated (7d)',
                'value' => $updatedWeek,
                'hint' => 'Recently edited',
                'color' => 'amber',
                'icon' => 'spark',
            ],
        ];
    }
}
