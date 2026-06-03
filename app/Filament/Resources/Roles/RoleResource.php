<?php

namespace App\Filament\Resources\Roles;

use App\Filament\Support\NavigationGroups;
use App\Support\FilamentPermissions;
use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\Schemas\RoleForm;
use App\Filament\Resources\Roles\Tables\RolesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationLabel = 'Role';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 2;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::AUTHENTICATION;

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
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
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
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
        return $record->name !== 'Super Admin' && FilamentPermissions::canManageAccessControl();
    }

    public static function canDeleteAny(): bool
    {
        return FilamentPermissions::canManageAccessControl();
    }
}
