<?php

namespace App\Filament\Pages\Tables;

use App\Filament\Support\PortfolioEditorActions;
use App\Models\ContactMessage;
use App\Support\ContactMessageSanitizer;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(ContactMessage::query()->latest())
            ->heading('Inbox')
            ->description('Messages submitted from the public contact form.')
            ->emptyStateHeading('No messages yet')
            ->emptyStateDescription('When visitors send a message from your site contact page, it will appear here.')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('message')
                    ->formatStateUsing(fn (string $state): string => ContactMessageSanitizer::plainText($state))
                    ->limit(60)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('read_at')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? 'Read' : 'New')
                    ->color(fn (?string $state): string => filled($state) ? 'gray' : 'success')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('read_at')
                    ->label('Read status')
                    ->nullable()
                    ->placeholder('All messages')
                    ->trueLabel('Read only')
                    ->falseLabel('Unread only')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('read_at'),
                        false: fn ($query) => $query->whereNull('read_at'),
                        blank: fn ($query) => $query,
                    ),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->modalHeading(fn (ContactMessage $record): string => 'Message from '.$record->name)
                        ->modalContent(fn (ContactMessage $record) => view('filament.partials.contact-message-modal', [
                            'record' => $record,
                        ]))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->after(fn (ContactMessage $record): mixed => $record->markAsRead()),
                    Action::make('markRead')
                        ->label('Mark as read')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn (ContactMessage $record): bool => ! $record->isRead())
                        ->action(function (ContactMessage $record, $livewire): void {
                            $record->markAsRead();

                            $livewire->dispatch(
                                'swal',
                                type: 'success',
                                title: 'Marked as read',
                                text: 'Message from '.$record->name.' updated.',
                            );
                        }),
                    Action::make('reply')
                        ->label('Reply by email')
                        ->icon('heroicon-o-envelope')
                        ->url(fn (ContactMessage $record): string => 'mailto:'.rawurlencode($record->email).'?subject='.rawurlencode('Re: Your message to '.config('app.name')))
                        ->openUrlInNewTab(),
                    DeleteAction::make()
                        ->requiresConfirmation(false)
                        ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                            'Delete this message?',
                            'This cannot be undone.',
                        ))
                        ->successNotification(null)
                        ->after(fn ($livewire): mixed => $livewire->dispatch(
                            'swal',
                            type: 'success',
                            title: 'Message deleted',
                            text: 'The message has been removed.',
                        )),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-horizontal'),
            ]);
    }
}
