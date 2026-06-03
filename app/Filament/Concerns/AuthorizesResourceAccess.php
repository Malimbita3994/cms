<?php

namespace App\Filament\Concerns;

use App\Support\FilamentPermissions;
use Illuminate\Database\Eloquent\Model;

trait AuthorizesResourceAccess
{
    public static function canAccess(): bool
    {
        return FilamentPermissions::canAccessResource(static::class);
    }

    public static function canViewAny(): bool
    {
        return FilamentPermissions::canViewAnyModel(static::getModel());
    }

    public static function canView(Model $record): bool
    {
        return FilamentPermissions::canViewModel(static::getModel())
            || FilamentPermissions::canUpdateModel(static::getModel());
    }

    public static function canCreate(): bool
    {
        return FilamentPermissions::canCreateModel(static::getModel());
    }

    public static function canEdit(Model $record): bool
    {
        return FilamentPermissions::canUpdateModel(static::getModel());
    }

    public static function canDelete(Model $record): bool
    {
        return FilamentPermissions::canDeleteModel(static::getModel());
    }

    public static function canDeleteAny(): bool
    {
        return FilamentPermissions::canDeleteAnyModel(static::getModel());
    }
}
