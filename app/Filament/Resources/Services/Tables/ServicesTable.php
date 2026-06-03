<?php

namespace App\Filament\Resources\Services\Tables;

use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Support\PortfolioEditorActions;
use App\Filament\Support\PortfolioModalRows;
use App\Filament\Support\PortfolioTableActions;
use App\Models\Service;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                IconColumn::make('is_published')
                    ->label('Live')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedEyeSlash)
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('icon')
                    ->badge(),
                TextColumn::make('description')
                    ->label('Summary')
                    ->formatStateUsing(fn (?string $state): string => Str::limit(strip_tags($state ?? ''), 60))
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                PortfolioTableActions::resourceGroup(
                    ServiceResource::class,
                    'service',
                    fn (Service $record): bool => ServiceResource::canView($record),
                    fn (Service $record): bool => ServiceResource::canEdit($record),
                    fn (Service $record): string => $record->title,
                    fn (Service $record) => view('filament.partials.portfolio-record-modal', PortfolioModalRows::service($record)),
                    fn (Service $record): string => ServiceResource::getUrl('edit', ['record' => $record]),
                    'Delete this service?',
                    'This service will be removed from the live site.',
                ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(false)
                        ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                            'Delete selected services?',
                            'All selected services will be removed permanently.',
                        ))
                        ->successNotification(null)
                        ->after(fn ($livewire): mixed => $livewire->dispatch(
                            'swal',
                            type: 'success',
                            title: 'Services deleted',
                            text: 'Selected services have been removed.',
                        )),
                ]),
            ]);
    }
}
