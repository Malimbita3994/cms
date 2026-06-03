<?php

namespace App\Filament\Resources\Profiles\Tables;

use App\Filament\Resources\Profiles\ProfileResource;
use App\Filament\Support\PortfolioEditorActions;
use App\Filament\Support\PortfolioModalRows;
use App\Filament\Support\PortfolioTableActions;
use App\Models\Profile;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_published')
                    ->label('Live')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckCircle)
                    ->falseIcon(Heroicon::OutlinedEyeSlash)
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('role')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('location')
                    ->searchable(),
                TextColumn::make('linkedin_url')
                    ->searchable(),
                TextColumn::make('github_url')
                    ->searchable(),
                TextColumn::make('image')
                    ->limit(36),
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
                    ProfileResource::class,
                    'profile',
                    fn (Profile $record): bool => ProfileResource::canView($record),
                    fn (Profile $record): bool => ProfileResource::canEdit($record),
                    fn (Profile $record): string => $record->name,
                    fn (Profile $record) => view('filament.partials.portfolio-record-modal', PortfolioModalRows::profile($record)),
                    fn (Profile $record): string => ProfileResource::getUrl('edit', ['record' => $record]),
                    'Delete this profile?',
                    'This profile record will be removed from the CMS.',
                ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(false)
                        ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                            'Delete selected profiles?',
                            'All selected profiles will be removed permanently.',
                        ))
                        ->successNotification(null)
                        ->after(fn ($livewire): mixed => $livewire->dispatch(
                            'swal',
                            type: 'success',
                            title: 'Profiles deleted',
                            text: 'Selected profiles have been removed.',
                        )),
                ]),
            ]);
    }
}
