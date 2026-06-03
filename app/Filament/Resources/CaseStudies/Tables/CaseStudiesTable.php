<?php

namespace App\Filament\Resources\CaseStudies\Tables;

use App\Filament\Resources\CaseStudies\CaseStudyResource;
use App\Filament\Support\PortfolioEditorActions;
use App\Filament\Support\PortfolioTableActions;
use App\Models\CaseStudy;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CaseStudiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->numeric()
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
                    ->sortable()
                    ->wrap(),
                TextColumn::make('impact')
                    ->limit(50)
                    ->formatStateUsing(fn (?string $state): string => trim(strip_tags((string) $state)))
                    ->toggleable(),
                TextColumn::make('stack')
                    ->label('Stack')
                    ->formatStateUsing(fn ($state): string => is_array($state) ? (string) count(array_filter($state)) : '0')
                    ->alignCenter(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                PortfolioTableActions::resourceGroup(
                    CaseStudyResource::class,
                    'case study',
                    fn (CaseStudy $record): bool => CaseStudyResource::canView($record),
                    fn (CaseStudy $record): bool => CaseStudyResource::canEdit($record),
                    fn (CaseStudy $record): string => $record->title,
                    fn (CaseStudy $record) => view('filament.partials.case-study-modal', [
                        'record' => $record,
                    ]),
                    fn (CaseStudy $record): string => CaseStudyResource::getUrl('edit', ['record' => $record]),
                    'Delete this case study?',
                    'This case study will be removed from the live site.',
                ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(false)
                        ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                            'Delete selected case studies?',
                            'All selected case studies will be removed permanently.',
                        ))
                        ->successNotification(null)
                        ->after(fn ($livewire): mixed => $livewire->dispatch(
                            'swal',
                            type: 'success',
                            title: 'Case studies deleted',
                            text: 'Selected case studies have been removed.',
                        )),
                ]),
            ]);
    }
}
