<?php

namespace App\Filament\Pages;

use App\Filament\Support\NavigationGroups;
use App\Models\User;
use App\Support\DashboardMetrics;
use App\Support\FilamentPermissions;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\View\View;

class Dashboard extends BaseDashboard
{
    public static function canAccess(): bool
    {
        if (FilamentPermissions::canAccessPage(static::class)) {
            return true;
        }

        return FilamentPermissions::hasAnyCmsPermission();
    }

    public function mountCanAuthorizeAccess(): void
    {
        $user = auth()->user();

        if ($user instanceof User && ! FilamentPermissions::hasAnyCmsPermission($user)) {
            Filament::auth()->logout();

            session()->flash('auth_alert', [
                'type' => 'error',
                'title' => 'No access',
                'text' => 'Your account has no permissions assigned. Ask an administrator to assign you a role.',
            ]);

            $this->redirect(Filament::getLoginUrl(), navigate: false);

            return;
        }

        if (! static::canAccess()) {
            $this->redirect(FilamentPermissions::resolvePanelHomeUrl(), navigate: false);

            return;
        }
    }

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 10;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::OVERVIEW;

    protected string $view = 'filament.pages.dashboard';

    /** @var array<int, array{day: string, total: int}> */
    public array $chartPoints = [];

    /** @var array<string, int> */
    public array $kpis = [];

    /** @var array<string, string> */
    public array $links = [];

    /** @var array<int, array{type: string, title: string, when: string}> */
    public array $recent = [];

    /** @var array<int, array{day: string, total: int}> */
    public array $activityTrend = [];

    /** @var array<string, int|bool> */
    public array $contentHealth = [];

    /** @var array{app_name: string, profile_name: string} */
    public array $branding = [
        'app_name' => 'Portfolio CMS',
        'profile_name' => 'Admin',
    ];

    public string $lastUpdated = '';

    public function getWidgets(): array
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

    public function mount(): void
    {
        $this->links = [
            'admin' => url('/admin'),
            'site' => rtrim(config('app.frontend_url', 'http://127.0.0.1:3000'), '/'),
            'site_api' => url('/api/v1/site'),
        ];

        $this->refreshKpiWidgets();
    }

    public function refreshKpiWidgets(): void
    {
        $bundle = DashboardMetrics::cached();

        $this->kpis = $bundle['kpis'];
        $this->recent = $bundle['recent'];
        $this->activityTrend = $bundle['activityTrend'];
        $this->chartPoints = $this->activityTrend;
        $this->contentHealth = $bundle['contentHealth'];
        $this->branding = $bundle['branding'];
        $this->lastUpdated = now()->format('H:i:s');
    }
}
