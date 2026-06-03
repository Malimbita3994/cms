<?php

namespace App\Filament\Resources\Skills\Tables;

use App\Filament\Resources\Skills\SkillResource;
use App\Filament\Support\PortfolioEditorActions;
use App\Filament\Support\PortfolioModalRows;
use App\Filament\Support\PortfolioTableActions;
use App\Models\Skill;
use App\Support\PortfolioAsset;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SkillsTable
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
                    ->getStateUsing(fn (Skill $record): ?string => PortfolioAsset::toUploadState($record->image))
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('level')
                    ->label('Proficiency')
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('icon')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('focus')
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
                    SkillResource::class,
                    'skill',
                    fn (Skill $record): bool => SkillResource::canView($record),
                    fn (Skill $record): bool => SkillResource::canEdit($record),
                    fn (Skill $record): string => $record->name,
                    fn (Skill $record) => view('filament.partials.portfolio-record-modal', PortfolioModalRows::skill($record)),
                    fn (Skill $record): string => SkillResource::getUrl('edit', ['record' => $record]),
                    'Delete this skill?',
                    'This skill will be removed from the homepage and /skills.',
                ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(false)
                        ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                            'Delete selected skills?',
                            'All selected skills will be removed permanently.',
                        ))
                        ->successNotification(null)
                        ->after(fn ($livewire): mixed => $livewire->dispatch(
                            'swal',
                            type: 'success',
                            title: 'Skills deleted',
                            text: 'Selected skills have been removed.',
                        )),
                ]),
            ]);
    }
}
