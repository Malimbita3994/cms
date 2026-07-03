<?php

namespace App\Filament\Support;

final class NavigationGroups
{
    public const OVERVIEW = 'Overview';

    public const SITE_PAGES = 'Site pages';

    public const PORTFOLIO = 'Portfolio';

    public const NEWS = 'News & updates';

    public const ACCESS_CONTROL = 'Access control';

    /** @deprecated Use specific groups above */
    public const CONTENT = self::OVERVIEW;

    /** @deprecated Use ACCESS_CONTROL */
    public const AUTHENTICATION = self::ACCESS_CONTROL;
}
