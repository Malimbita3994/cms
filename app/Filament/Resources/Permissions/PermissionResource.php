<?php

namespace App\Filament\Resources\Permissions;

use App\Filament\Support\NavigationGroups;
use App\Support\FilamentPermissions;
use App\Filament\Resources\Permissions\Pages\CreatePermission;
use App\Filament\Resources\Permissions\Pages\EditPermission;
use App\Filament\Resources\Permissions\Pages\ListPermissions;
use App\Filament\Resources\Permissions\Schemas\PermissionForm;
use App\Filament\Resources\Permissions\Tables\PermissionsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationLabel = 'Permissions';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLockClosed;

    protected static ?int $navigationSort = 30;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::ACCESS_CONTROL;

    public static function form(Schema $schema): Schema
    {
        return PermissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PermissionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermissions::route('/'),
            'create' => CreatePermission::route('/create'),
            'edit' => EditPermission::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return FilamentPermissions::canManageAccessControl();
    }

    public static function canViewAny(): bool
    {
        return FilamentPermissions::canManageAccessControl();
    }

    public static function canCreate(): bool
    {
        return FilamentPermissions::canManageAccessControl();
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return FilamentPermissions::canManageAccessControl();
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return FilamentPermissions::canManageAccessControl();
    }

    public static function canDeleteAny(): bool
    {
        return FilamentPermissions::canManageAccessControl();
    }
}
