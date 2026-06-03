<?php

namespace App\Filament\Resources\Insights\Tables;

use App\Filament\Resources\Insights\InsightResource;
use App\Filament\Support\PortfolioEditorActions;
use App\Filament\Support\PortfolioModalRows;
use App\Filament\Support\PortfolioTableActions;
use App\Models\Insight;
use App\Support\PortfolioAsset;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class InsightsTable
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
                ImageColumn::make('image')
                    ->label('Image')
                    ->disk(PortfolioAsset::DISK)
                    ->getStateUsing(fn (Insight $record): ?string => PortfolioAsset::toUploadState($record->image))
                    ->toggleable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('display_date')
                    ->label('Date')
                    ->searchable(),
                TextColumn::make('excerpt')
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
                    InsightResource::class,
                    'insight',
                    fn (Insight $record): bool => InsightResource::canView($record),
                    fn (Insight $record): bool => InsightResource::canEdit($record),
                    fn (Insight $record): string => $record->title,
                    fn (Insight $record) => view('filament.partials.portfolio-record-modal', PortfolioModalRows::insight($record)),
                    fn (Insight $record): string => InsightResource::getUrl('edit', ['record' => $record]),
                    'Delete this insight?',
                    'This insight will be removed from /insights.',
                ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(false)
                        ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                            'Delete selected insights?',
                            'All selected insights will be removed permanently.',
                        ))
                        ->successNotification(null)
                        ->after(fn ($livewire): mixed => $livewire->dispatch(
                            'swal',
                            type: 'success',
                            title: 'Insights deleted',
                            text: 'Selected insights have been removed.',
                        )),
                ]),
            ]);
    }
}
