<?php

namespace App\Filament\Resources\PortfolioProjects\Tables;

use App\Filament\Resources\PortfolioProjects\PortfolioProjectResource;
use App\Filament\Support\PortfolioEditorActions;
use App\Filament\Support\PortfolioModalRows;
use App\Filament\Support\PortfolioTableActions;
use App\Models\PortfolioProject;
use App\Support\PortfolioAsset;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PortfolioProjectsTable
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
                    ->getStateUsing(fn (PortfolioProject $record): ?string => PortfolioAsset::toUploadState($record->image))
                    ->toggleable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Summary')
                    ->formatStateUsing(fn (?string $state): string => Str::limit(strip_tags($state ?? ''), 60))
                    ->toggleable(),
                TextColumn::make('technologies')
                    ->label('Tech')
                    ->formatStateUsing(fn ($state): string => is_array($state) ? (string) count($state) : '0')
                    ->badge(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                PortfolioTableActions::resourceGroup(
                    PortfolioProjectResource::class,
                    'project',
                    fn (PortfolioProject $record): bool => PortfolioProjectResource::canView($record),
                    fn (PortfolioProject $record): bool => PortfolioProjectResource::canEdit($record),
                    fn (PortfolioProject $record): string => $record->title,
                    fn (PortfolioProject $record) => view('filament.partials.portfolio-record-modal', PortfolioModalRows::project($record)),
                    fn (PortfolioProject $record): string => PortfolioProjectResource::getUrl('edit', ['record' => $record]),
                    'Delete this project?',
                    'This project will be removed from the live site.',
                ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(false)
                        ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                            'Delete selected projects?',
                            'All selected projects will be removed permanently.',
                        ))
                        ->successNotification(null)
                        ->after(fn ($livewire): mixed => $livewire->dispatch(
                            'swal',
                            type: 'success',
                            title: 'Projects deleted',
                            text: 'Selected projects have been removed.',
                        )),
                ]),
            ]);
    }
}
