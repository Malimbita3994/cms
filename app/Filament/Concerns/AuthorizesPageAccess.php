<?php

namespace App\Filament\Concerns;

use App\Support\FilamentPermissions;

trait AuthorizesPageAccess
{
    public static function canAccess(): bool
    {
        return FilamentPermissions::canAccessPage(static::class);
    }
}
