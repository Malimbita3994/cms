<?php

namespace App\Filament\Resources\CareerTimelineEntries\Tables;

use App\Filament\Resources\CareerTimelineEntries\CareerTimelineEntryResource;
use App\Filament\Support\PortfolioEditorActions;
use App\Filament\Support\PortfolioModalRows;
use App\Filament\Support\PortfolioTableActions;
use App\Models\CareerTimelineEntry;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CareerTimelineEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_published')
                    ->label('Live')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedEyeSlash)
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('period_label')
                    ->label('Year / period')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Role / title')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('description')
                    ->label('Summary')
                    ->limit(60)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                PortfolioTableActions::resourceGroup(
                    CareerTimelineEntryResource::class,
                    'career entry',
                    fn (CareerTimelineEntry $record): bool => CareerTimelineEntryResource::canView($record),
                    fn (CareerTimelineEntry $record): bool => CareerTimelineEntryResource::canEdit($record),
                    fn (CareerTimelineEntry $record): string => $record->title,
                    fn (CareerTimelineEntry $record) => view('filament.partials.portfolio-record-modal', PortfolioModalRows::career($record)),
                    fn (CareerTimelineEntry $record): string => CareerTimelineEntryResource::getUrl('edit', ['record' => $record]),
                    'Delete this career entry?',
                    'It will be removed from the Career Journey section on your site.',
                ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(false)
                        ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                            'Delete selected career entries?',
                            'All selected entries will be removed permanently.',
                        ))
                        ->successNotification(null)
                        ->after(fn ($livewire): mixed => $livewire->dispatch(
                            'swal',
                            type: 'success',
                            title: 'Career entries deleted',
                            text: 'Selected entries have been removed.',
                        )),
                ]),
            ]);
    }
}
