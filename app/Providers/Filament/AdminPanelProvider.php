<?php

namespace App\Providers\Filament;

use App\Filament\Auth\EditProfile;
use App\Filament\Auth\RequestPasswordReset;
use App\Filament\Pages\Dashboard;
use App\Filament\Support\NavigationGroups;
use App\Http\Middleware\EnsureHasCmsPermissions;
use App\Http\Middleware\RedirectGuestsToCmsLogin;
use App\Http\Middleware\RedirectInvalidSessionToCmsLogin;
use Filament\Enums\ThemeMode;
use Filament\Enums\UserMenuPosition;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('BA CMS')
            ->brandLogo(asset('loggoo.png'))
            ->darkModeBrandLogo(asset('loggoo.png'))
            ->brandLogoHeight('1.75rem')
            ->login(null)
            ->passwordReset(requestAction: RequestPasswordReset::class)
            ->passwordResetRequestRouteSlug('')
            ->profile(EditProfile::class, isSimple: false)
            ->userMenu(position: UserMenuPosition::Topbar)
            ->sidebarWidth('16rem')
            ->maxContentWidth(Width::Full)
            ->darkMode(true, isForced: true)
            ->defaultThemeMode(ThemeMode::Dark)
            ->font('Inter')
            ->viteTheme('resources/css/filament/metronic.css')
            ->navigationGroups([
                NavigationGroup::make(NavigationGroups::OVERVIEW)
                    ->collapsible()
                    ->collapsed(false),
                NavigationGroup::make(NavigationGroups::SITE_PAGES)
                    ->collapsible()
                    ->collapsed(true),
                NavigationGroup::make(NavigationGroups::PORTFOLIO)
                    ->collapsible()
                    ->collapsed(true),
                NavigationGroup::make(NavigationGroups::NEWS)
                    ->collapsible()
                    ->collapsed(true),
                NavigationGroup::make(NavigationGroups::ACCESS_CONTROL)
                    ->collapsible()
                    ->collapsed(true),
            ])
            ->colors([
                'primary' => [
                    50 => '#fffbeb',
                    100 => '#fef3c7',
                    200 => '#fde68a',
                    300 => '#fcd34d',
                    400 => '#fbbf24',
                    500 => '#f59e0b',
                    600 => '#ea580c',
                    700 => '#c2410c',
                    800 => '#9a3412',
                    900 => '#7c2d12',
                    950 => '#451a03',
                ],
            ])
            ->spa()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                RedirectInvalidSessionToCmsLogin::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                RedirectGuestsToCmsLogin::class,
                EnsureHasCmsPermissions::class,
            ], isPersistent: true);
    }
}
