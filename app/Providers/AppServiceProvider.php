<?php

namespace App\Providers;

use App\Filament\Auth\Http\Responses\LoginResponse;
use App\Filament\Auth\Http\Responses\LogoutResponse;
use App\Filament\Support\PortfolioFormFields;
use App\Filament\Support\SidebarNavigationConfig;
use Filament\Forms\Components\RichEditor;
use App\Models\CareerTimelineEntry;
use App\Models\CaseStudy;
use App\Models\ContactMessage;
use App\Models\HomePage;
use App\Models\Insight;
use App\Models\PortfolioProject;
use App\Models\Profile;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\User;
use App\Support\SiteContentCache;
use App\Observers\ContentActivityObserver;
use App\Services\AdminNotificationService;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LoginResponseContract::class, LoginResponse::class);
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('local') && ! $this->app->runningInConsole()) {
            @ini_set('max_execution_time', '120');
            @set_time_limit(120);
        }

        RichEditor::configureUsing(
            fn (RichEditor $editor): RichEditor => PortfolioFormFields::applyRichEditorDefaults($editor),
        );

        foreach ([PortfolioProject::class, Insight::class, Service::class, Skill::class, CaseStudy::class, CareerTimelineEntry::class, ContactMessage::class] as $model) {
            $model::observe(ContentActivityObserver::class);
        }

        foreach ([Profile::class, SiteSetting::class, HomePage::class] as $model) {
            $model::saved(static fn () => SiteContentCache::flush());
        }

        Gate::before(static function (?User $user): ?bool {
            if (! $user) {
                return null;
            }

            if ($user->hasRole('Super Admin')) {
                return true;
            }

            return null;
        });

        Gate::define('manage-access-control', static function (User $user): bool {
            return $user->hasPermissionTo('manage-access-control');
        });

        FilamentView::registerRenderHook(
            PanelsRenderHook::STYLES_AFTER,
            fn (): ?string => auth()->check()
                ? self::cachedFilamentPartial('panel-assets-vite', 'filament.partials.panel-assets-vite')
                    .view('filament.partials.panel-assets-session')->render()
                    .self::renderAuthUserAvatarScript()
                : null,
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_LOGO_BEFORE,
            fn (): string => self::cachedFilamentPartial('topbar-brand', 'filament.partials.topbar-brand'),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_START,
            fn (): string => self::cachedFilamentPartial('topbar-search', 'filament.partials.topbar-search'),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_LOGO_AFTER,
            fn (): string => self::cachedFilamentPartial('topbar-title', 'filament.partials.topbar-title'),
        );

        /* Bell + user menu live together in .fi-topbar-end (top-right) */
        FilamentView::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_AFTER,
            fn (): string => self::cachedFilamentPartial('topbar-actions', 'filament.partials.topbar-actions'),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_NAV_END,
            fn (): string => self::cachedFilamentPartial(
                'sidebar-nav-config.v'.SidebarNavigationConfig::VERSION,
                'filament.partials.sidebar-nav-config',
            ),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_FOOTER,
            fn (): string => self::cachedFilamentPartial('sidebar-logout', 'filament.partials.sidebar-logout'),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn (): ?string => auth()->check()
                ? self::cachedFilamentPartial('workspace-shell', 'filament.partials.workspace-shell')
                : null,
        );
    }

    private static function cachedFilamentPartial(string $key, string $view): string
    {
        $manifestPath = public_path('build/manifest.json');
        $manifestVersion = is_file($manifestPath) ? (string) filemtime($manifestPath) : '0';

        return Cache::rememberForever(
            "filament.partial.{$key}.{$manifestVersion}",
            static fn (): string => view($view)->render(),
        );
    }

    private static function renderAuthUserAvatarScript(): string
    {
        $user = auth()->user();

        if ($user === null) {
            return '';
        }

        $avatarUrl = \Filament\Facades\Filament::getUserAvatarUrl($user);

        return '<script type="application/json" id="auth-user-avatar-url">'
            .json_encode($avatarUrl, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)
            .'</script>';
    }
}
