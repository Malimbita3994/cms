<?php

namespace App\Filament\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Role')
                    ->weight('medium')
                    ->searchable(),
                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissions')
                    ->badge()
                    ->color('info'),
                TextColumn::make('guard_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('viewPermissions')
                        ->label('View permissions')
                        ->icon('heroicon-o-eye')
                        ->modalHeading(fn ($record): string => "Permissions for {$record->name}")
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalContent(function ($record): HtmlString {
                            $permissions = $record->permissions()
                                ->orderBy('name')
                                ->pluck('name');

                            if ($permissions->isEmpty()) {
                                return new HtmlString(
                                    '<div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">'.
                                    '<div class="flex items-center gap-2">'.
                                    '<span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400">'.
                                    '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.172 7.707 8.879a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'.
                                    '</span>'.
                                    '<div>'.
                                    '<div class="text-sm font-semibold text-gray-900 dark:text-gray-100">No permissions assigned</div>'.
                                    '<div class="text-xs text-gray-500 dark:text-gray-400">This role currently has no permissions.</div>'.
                                    '</div>'.
                                    '</div>'.
                                    '</div>'
                                );
                            }

                            $chips = $permissions
                                ->map(fn (string $permission): string => '<span class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-700 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-200">'.e($permission).'</span>')
                                ->implode('');

                            return new HtmlString(
                                '<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">'.
                                '<div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-3 dark:border-gray-800">'.
                                '<div class="flex items-center gap-3">'.
                                '<span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400">'.
                                '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 10.818l2.47-1.424a.75.75 0 10-.75-1.299l-2.47 1.424-2.47-1.424a.75.75 0 10-.75 1.3l2.47 1.423v2.85l-2.47 1.424a.75.75 0 10.75 1.299l2.47-1.424 2.47 1.424a.75.75 0 10.75-1.3l-2.47-1.423v-2.85z"/></svg>'.
                                '</span>'.
                                '<div>'.
                                '<div class="text-sm font-semibold text-gray-900 dark:text-gray-100">Assigned permissions</div>'.
                                '<div class="text-xs text-gray-500 dark:text-gray-400">These permissions are currently granted to this role.</div>'.
                                '</div>'.
                                '</div>'.
                                '<span class="inline-flex items-center rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-300">Total: '.$permissions->count().'</span>'.
                                '</div>'.
                                '<div class="max-h-96 overflow-y-auto p-4">'.
                                '<div class="flex flex-wrap gap-2">'.$chips.'</div>'.
                                '</div>'.
                                '</div>'
                            );
                        }),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
