<?php

namespace App\Filament\Resources\Users;

use App\Filament\Support\NavigationGroups;
use App\Support\FilamentPermissions;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Users';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 1;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::AUTHENTICATION;

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
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
