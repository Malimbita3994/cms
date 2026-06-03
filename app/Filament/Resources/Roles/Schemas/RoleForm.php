<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(Role::class, ignoreRecord: true)
                            ->disabled(fn (?Role $record): bool => $record?->name === 'Super Admin'),
                        Hidden::make('guard_name')
                            ->default('web')
                            ->dehydrated(),
                    ])
                    ->columns(1),
                Section::make('Permissions')
                    ->description('Users inherit these permissions when assigned this role.')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->relationship(
                                name: 'permissions',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->where('guard_name', 'web')->orderBy('name'),
                            )
                            ->columns(2)
                            ->gridDirection('row')
                            ->searchable()
                            ->bulkToggleable()
                            ->disabled(fn (?Role $record): bool => $record?->name === 'Super Admin')
                            ->helperText(fn (?Role $record): ?string => $record?->name === 'Super Admin'
                                ? 'Super Admin always has every permission.'
                                : 'Select what this role may do in the CMS.')
                            ->getOptionLabelFromRecordUsing(
                                fn (Permission $permission): string => static::labelForPermission($permission->name),
                            ),
                    ]),
            ]);
    }

    public static function labelForPermission(string $name): string
    {
        return match ($name) {
            'manage-access-control' => 'Manage users, roles & permissions',
            default => Str::headline(str_replace('_', ' ', $name)),
        };
    }
}
