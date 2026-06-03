<?php

namespace App\Filament\Support;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

final class PortfolioTableActions
{
    /**
     * Three-dot flyout: view modal, edit, publish/unpublish, delete (SweetAlert).
     *
     * @param  class-string  $resourceClass  Filament resource class
     * @param  callable(Model): bool  $canView
     * @param  callable(Model): bool  $canEdit
     * @param  callable(Model): string  $modalHeading
     * @param  callable(Model): \Illuminate\Contracts\View\View|string  $modalContent
     * @param  callable(Model): string  $editUrl
     */
    public static function resourceGroup(
        string $resourceClass,
        string $recordLabel,
        callable $canView,
        callable $canEdit,
        callable $modalHeading,
        callable $modalContent,
        callable $editUrl,
        string $deleteTitle,
        string $deleteText,
        bool $publishable = true,
    ): ActionGroup {
        $actions = [
            Action::make('view')
                ->label('View')
                ->icon('heroicon-o-eye')
                ->authorize($canView)
                ->modalHeading($modalHeading)
                ->modalContent($modalContent)
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalFooterActions([
                    Action::make('openEditor')
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->url($editUrl),
                ]),
            EditAction::make()
                ->label('Edit')
                ->url($editUrl),
        ];

        if ($publishable) {
            $actions[] = Action::make('unpublish')
                ->label('Unpublish')
                ->icon(Heroicon::OutlinedEyeSlash)
                ->color('warning')
                ->visible(fn (Model $record): bool => (bool) ($record->is_published ?? true))
                ->authorize($canEdit)
                ->requiresConfirmation(false)
                ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                    "Unpublish this {$recordLabel}?",
                    'It will be hidden from the public site until you publish it again.',
                    'Yes, unpublish',
                ))
                ->action(fn (Model $record) => $record->update(['is_published' => false]))
                ->successNotification(null)
                ->after(fn ($livewire): mixed => $livewire->dispatch(
                    'swal',
                    type: 'success',
                    title: ucfirst($recordLabel).' unpublished',
                    text: 'Hidden from the live site.',
                ));

            $actions[] = Action::make('publish')
                ->label('Publish')
                ->icon(Heroicon::OutlinedEye)
                ->color('success')
                ->visible(fn (Model $record): bool => ! (bool) ($record->is_published ?? true))
                ->authorize($canEdit)
                ->requiresConfirmation(false)
                ->action(fn (Model $record) => $record->update(['is_published' => true]))
                ->successNotification(null)
                ->after(fn ($livewire): mixed => $livewire->dispatch(
                    'swal',
                    type: 'success',
                    title: ucfirst($recordLabel).' published',
                    text: 'Visible on the live site.',
                ));
        }

        $actions[] = DeleteAction::make()
            ->label('Delete')
            ->requiresConfirmation(false)
            ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                $deleteTitle,
                $deleteText,
            ))
            ->successNotification(null)
            ->after(fn ($livewire): mixed => $livewire->dispatch(
                'swal',
                type: 'success',
                title: ucfirst($recordLabel).' deleted',
                text: 'The record has been removed.',
            ));

        return ActionGroup::make($actions)
            ->label('Actions')
            ->iconButton()
            ->icon('heroicon-m-ellipsis-vertical')
            ->tooltip('Actions')
            ->dropdown()
            ->dropdownPlacement('bottom-end');
    }
}
