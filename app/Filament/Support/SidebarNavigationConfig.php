<?php

namespace App\Filament\Support;

final class SidebarNavigationConfig
{
    public const VERSION = 2;

    /**
     * Group labels collapsed on first visit / after a nav version bump.
     * Overview stays expanded by default.
     *
     * @return list<string>
     */
    public static function defaultCollapsedLabels(): array
    {
        return [
            NavigationGroups::SITE_PAGES,
            NavigationGroups::PORTFOLIO,
            NavigationGroups::NEWS,
            NavigationGroups::ACCESS_CONTROL,
        ];
    }

    /**
     * @return array{version: int, defaultCollapsed: list<string>}
     */
    public static function scriptConfig(): array
    {
        return [
            'version' => self::VERSION,
            'defaultCollapsed' => self::defaultCollapsedLabels(),
        ];
    }
}
