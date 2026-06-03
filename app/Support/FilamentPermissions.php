<?php

namespace App\Support;

use App\Filament\Pages\ChangePassword;
use App\Filament\Pages\Dashboard;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Str;

final class FilamentPermissions
{
    public static function hasAnyCmsPermission(?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        return $user->getAllPermissions()->isNotEmpty();
    }

    public static function resolvePanelHomeUrl(): string
    {
        $panel = Filament::getCurrentOrDefaultPanel();

        if (Dashboard::canAccess()) {
            return Dashboard::getUrl();
        }

        foreach ($panel->getPages() as $pageClass) {
            if ($pageClass === ChangePassword::class) {
                continue;
            }

            if ($pageClass::canAccess()) {
                return $pageClass::getUrl();
            }
        }

        foreach ($panel->getResources() as $resourceClass) {
            if ($resourceClass::canAccess()) {
                return $resourceClass::getUrl();
            }
        }

        return $panel->getLoginUrl();
    }

    public static function userCan(string $permission): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->can($permission);
    }

    public static function modelPrefix(string $modelClass): string
    {
        return Str::snake(class_basename($modelClass));
    }

    public static function pagePermission(string $pageClass): string
    {
        return 'view_'.Str::snake(class_basename($pageClass));
    }

    public static function canViewAnyModel(string $modelClass): bool
    {
        return static::userCan('view_any_'.static::modelPrefix($modelClass));
    }

    public static function canViewModel(string $modelClass): bool
    {
        return static::userCan('view_'.static::modelPrefix($modelClass));
    }

    public static function canCreateModel(string $modelClass): bool
    {
        return static::userCan('create_'.static::modelPrefix($modelClass));
    }

    public static function canUpdateModel(string $modelClass): bool
    {
        return static::userCan('update_'.static::modelPrefix($modelClass));
    }

    public static function canDeleteModel(string $modelClass): bool
    {
        return static::userCan('delete_'.static::modelPrefix($modelClass));
    }

    public static function canDeleteAnyModel(string $modelClass): bool
    {
        return static::userCan('delete_any_'.static::modelPrefix($modelClass));
    }

    public static function canAccessPage(string $pageClass): bool
    {
        return static::userCan(static::pagePermission($pageClass));
    }

    public static function canAccessResource(string $resourceClass): bool
    {
        return static::canViewAnyModel($resourceClass::getModel());
    }

    public static function canManageAccessControl(): bool
    {
        return static::userCan('manage-access-control');
    }
}
