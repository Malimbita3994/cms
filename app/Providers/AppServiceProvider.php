<?php

namespace App\Providers;

use App\Filament\Auth\Http\Responses\LoginResponse;
use App\Filament\Auth\Http\Responses\LogoutResponse;
use App\Filament\Support\PortfolioFormFields;
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
                ? view('filament.partials.panel-assets')->render()
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
            fn (): string => (string) view('filament.partials.topbar-title'),
        );

        /* Bell + user menu live together in .fi-topbar-end (top-right) */
        FilamentView::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_AFTER,
            fn (): string => view('filament.partials.topbar-actions')->render(),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_NAV_END,
            fn (): string => view('filament.partials.sidebar-nav-config')->render(),
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
        return Cache::rememberForever(
            "filament.partial.{$key}",
            static fn (): string => view($view)->render(),
        );
    }
}
