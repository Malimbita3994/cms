<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Support\PortfolioEditorActions;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->badge()
                    ->separator(','),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                    ->color(fn (bool $state): string => $state ? 'success' : 'warning')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All users')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->url(fn (User $record): string => \App\Filament\Resources\Users\UserResource::getUrl('edit', ['record' => $record])),
                    Action::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (User $record): bool => ! $record->is_active)
                        ->requiresConfirmation(false)
                        ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                            'Activate this user?',
                            'They will be able to sign in to the admin panel again.',
                            'Activate',
                        ))
                        ->action(function (User $record, $livewire): void {
                            $record->update(['is_active' => true]);

                            $livewire->dispatch(
                                'swal',
                                type: 'success',
                                title: 'User activated',
                                text: $record->name.' can sign in again.',
                            );

                            $livewire->loadListStats();
                        }),
                    Action::make('inactivate')
                        ->label('Inactivate')
                        ->icon('heroicon-o-no-symbol')
                        ->color('warning')
                        ->visible(fn (User $record): bool => $record->is_active && $record->id !== auth()->id())
                        ->requiresConfirmation(false)
                        ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                            'Inactivate this user?',
                            'They will no longer be able to sign in.',
                            'Inactivate',
                        ))
                        ->action(function (User $record, $livewire): void {
                            $record->update(['is_active' => false]);

                            $livewire->dispatch(
                                'swal',
                                type: 'success',
                                title: 'User inactivated',
                                text: $record->name.' can no longer sign in.',
                            );

                            $livewire->loadListStats();
                        }),
                    Action::make('resetPassword')
                        ->label('Reset password')
                        ->icon('heroicon-o-key')
                        ->form([
                            TextInput::make('password')
                                ->password()
                                ->revealable()
                                ->required()
                                ->minLength(8)
                                ->confirmed(),
                            TextInput::make('password_confirmation')
                                ->password()
                                ->revealable()
                                ->required()
                                ->minLength(8)
                                ->dehydrated(false),
                        ])
                        ->action(function (User $record, array $data, $livewire): void {
                            $record->update([
                                'password' => $data['password'],
                            ]);

                            $livewire->dispatch(
                                'swal',
                                type: 'success',
                                title: 'Password reset',
                                text: 'The user can sign in with the new password.',
                            );
                        }),
                    DeleteAction::make()
                        ->hidden(fn (User $record): bool => $record->id === auth()->id())
                        ->requiresConfirmation(false)
                        ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                            'Delete this user?',
                            'This account will be removed permanently.',
                        ))
                        ->successNotification(null)
                        ->after(fn ($livewire): mixed => $livewire->dispatch(
                            'swal',
                            type: 'success',
                            title: 'User deleted',
                            text: 'The account has been removed.',
                        )),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(false)
                        ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                            'Delete selected users?',
                            'All selected accounts will be removed permanently.',
                        ))
                        ->successNotification(null)
                        ->after(fn ($livewire): mixed => $livewire->dispatch(
                            'swal',
                            type: 'success',
                            title: 'Users deleted',
                            text: 'Selected accounts have been removed.',
                        )),
                ]),
            ]);
    }
}
