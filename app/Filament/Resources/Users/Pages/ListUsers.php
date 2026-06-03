<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\InteractsWithPortfolioListStats;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Support\Arr;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListUsers extends ListRecords
{
    use InteractsWithPortfolioListStats;

    protected static string $resource = UserResource::class;

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
        $total = User::query()->count();
        $withRoles = User::query()->whereHas('roles')->count();
        $active = User::query()->where('is_active', true)->count();
        $inactive = User::query()->where('is_active', false)->count();

        $share = static fn (int $count): int => $total > 0 ? (int) round(($count / $total) * 100) : 0;

        $indexUrl = UserResource::getUrl('index');

        return [
            [
                'label' => 'Total users',
                'value' => $total,
                'hint' => 'All admin accounts',
                'color' => 'orange',
                'icon' => 'users',
                'share' => $total > 0 ? 100 : 0,
                'show_share' => false,
                'show_track' => false,
                'url' => $indexUrl,
            ],
            [
                'label' => 'With roles',
                'value' => $withRoles,
                'hint' => 'At least one role assigned',
                'color' => 'blue',
                'icon' => 'shield',
                'share' => $share($withRoles),
                'url' => url('/admin/roles'),
            ],
            [
                'label' => 'Active',
                'value' => $active,
                'hint' => 'Can sign in to admin',
                'color' => 'green',
                'icon' => 'active',
                'share' => $share($active),
                'url' => $indexUrl.'?'.Arr::query([
                    'tableFilters' => [
                        'is_active' => ['value' => '1'],
                    ],
                ]),
            ],
            [
                'label' => 'Inactive',
                'value' => $inactive,
                'hint' => 'Sign-in disabled',
                'color' => 'amber',
                'icon' => 'inactive',
                'share' => $share($inactive),
                'url' => $indexUrl.'?'.Arr::query([
                    'tableFilters' => [
                        'is_active' => ['value' => '0'],
                    ],
                ]),
            ],
        ];
    }
}
