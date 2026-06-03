<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

final class AdminUserForm
{
    public static function configure(Schema $schema, bool $creating): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make()
                    ->extraAttributes(['class' => 'home-editor-card home-editor-card--main'])
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(User::class, ignoreRecord: true),
                        Select::make('roles')
                            ->label('Roles')
                            ->options(fn (): array => Role::query()->orderBy('name')->pluck('name', 'name')->all())
                            ->multiple()
                            ->preload()
                            ->native(false),
                        Toggle::make('is_active')
                            ->label('Active account')
                            ->helperText('Inactive users cannot sign in to the admin panel.')
                            ->default(true)
                            ->disabled(fn (?User $record): bool => $record?->id === auth()->id())
                            ->inline(false),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->label('Password')
                            ->required($creating)
                            ->minLength(8)
                            ->helperText($creating ? 'Minimum 8 characters.' : 'Leave blank to keep the current password.')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->confirmed(),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->revealable()
                            ->label('Confirm password')
                            ->required($creating)
                            ->minLength(8)
                            ->dehydrated(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
