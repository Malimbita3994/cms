<?php

namespace App\Filament\Concerns;

use App\Filament\Support\PortfolioEditorActions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

trait SingletonEditorActions
{
    /**
     * @param  callable(): Model|null  $resolveRecord
     * @param  callable(Model): \Illuminate\Contracts\View\View|string  $modalContent
     * @param  callable(Model): string  $modalHeading
     */
    protected function singletonEditorActionGroup(
        string $label,
        callable $resolveRecord,
        callable $modalHeading,
        callable $modalContent,
        bool $publishable = true,
    ): ActionGroup {
        return ActionGroup::make([
            Action::make('view')
                ->label('View')
                ->icon('heroicon-o-eye')
                ->modalHeading(function () use ($resolveRecord, $modalHeading): string {
                    $record = $resolveRecord();

                    return $record ? $modalHeading($record) : $label;
                })
                ->modalContent(function () use ($resolveRecord, $modalContent) {
                    $record = $resolveRecord();

                    return $record ? $modalContent($record) : view('filament.partials.portfolio-record-modal', [
                        'title' => $label,
                        'status' => null,
                        'rows' => [['label' => 'Content', 'text' => 'No record found.']],
                    ]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->disabled(fn (): bool => $resolveRecord() === null),
            ...($publishable ? [
                Action::make('unpublish')
                    ->label('Unpublish')
                    ->icon(Heroicon::OutlinedEyeSlash)
                    ->color('warning')
                    ->visible(function () use ($resolveRecord): bool {
                        $record = $resolveRecord();

                        return $record && (bool) ($record->is_published ?? true);
                    })
                    ->requiresConfirmation(false)
                    ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                        "Unpublish {$label}?",
                        'It will be hidden from the public site until you publish it again.',
                        'Yes, unpublish',
                    ))
                    ->action(function () use ($resolveRecord): void {
                        $resolveRecord()?->update(['is_published' => false]);
                        $this->queueSiteRevalidation();
                    })
                    ->successNotification(null)
                    ->after(fn (): mixed => $this->dispatch(
                        'swal',
                        type: 'success',
                        title: 'Unpublished',
                        text: 'Hidden from the live site.',
                    )),
                Action::make('publish')
                    ->label('Publish')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('success')
                    ->visible(function () use ($resolveRecord): bool {
                        $record = $resolveRecord();

                        return $record && ! (bool) ($record->is_published ?? true);
                    })
                    ->requiresConfirmation(false)
                    ->action(function () use ($resolveRecord): void {
                        $resolveRecord()?->update(['is_published' => true]);
                        $this->queueSiteRevalidation();
                    })
                    ->successNotification(null)
                    ->after(fn (): mixed => $this->dispatch(
                        'swal',
                        type: 'success',
                        title: 'Published',
                        text: 'Visible on the live site.',
                    )),
            ] : []),
        ])
            ->label('Actions')
            ->iconButton()
            ->icon('heroicon-m-ellipsis-vertical')
            ->tooltip('Actions')
            ->dropdown()
            ->dropdownPlacement('bottom-end');
    }
}
