<?php

namespace App\Filament\Resources\Skills\Pages;

use App\Filament\Concerns\InteractsWithPortfolioListStats;
use App\Filament\Resources\Skills\SkillResource;
use App\Models\Skill;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSkills extends ListRecords
{
    use InteractsWithPortfolioListStats;

    protected static string $resource = SkillResource::class;

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
        $total = Skill::query()->count();
        $withImage = Skill::query()
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->count();
        $avgLevel = (int) round((float) Skill::query()->avg('level'));
        $updatedWeek = Skill::query()
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        return [
            [
                'label' => 'Total skills',
                'value' => $total,
                'hint' => 'On homepage & /skills',
                'color' => 'orange',
                'icon' => 'skill',
            ],
            [
                'label' => 'With images',
                'value' => $withImage,
                'hint' => 'Custom card artwork',
                'color' => 'blue',
                'icon' => 'image',
            ],
            [
                'label' => 'Avg proficiency',
                'value' => $avgLevel.'%',
                'hint' => 'Across all skills',
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
