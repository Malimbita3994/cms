<?php

namespace App\Filament\Resources\Posters\Tables;

use App\Filament\Resources\Posters\PosterResource;
use App\Filament\Support\PortfolioEditorActions;
use App\Filament\Support\PortfolioModalRows;
use App\Filament\Support\PortfolioTableActions;
use App\Filament\Support\PosterCategoryColors;
use App\Models\Poster;
use App\Support\PortfolioAsset;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PostersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->searchPlaceholder('Search posters/news...')
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([5, 10, 25, 50])
            ->deferFilters(false)
            ->columnManager(false)
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->filtersResetActionPosition(FiltersResetActionPosition::Header)
            ->emptyStateIcon(Heroicon::OutlinedNewspaper)
            ->emptyStateHeading('No posters yet')
            ->emptyStateDescription('Create your first poster to publish news or updates on the website.')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Create Poster')
                    ->icon(Heroicon::Plus)
                    ->url(PosterResource::getUrl('create')),
            ])
            ->columns([
                ImageColumn::make('image')
                    ->label('Preview')
                    ->disk(PortfolioAsset::DISK)
                    ->getStateUsing(fn (Poster $record): ?string => PortfolioAsset::toUploadState($record->image))
                    ->imageWidth('4.5rem')
                    ->imageHeight('3rem')
                    ->extraImgAttributes(['class' => 'poster-table-preview-img'])
                    ->defaultImageUrl(null)
                    ->toggleable(false),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable(['title', 'slug', 'short_description'])
                    ->sortable()
                    ->grow()
                    ->description(fn (Poster $record): string => '/updates/'.$record->slug),
                TextColumn::make('category')
                    ->label('Category')
                    ->formatStateUsing(fn (string $state): string => PosterCategoryColors::badgeHtml($state))
                    ->html()
                    ->searchable()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('is_published')
                    ->label('Status')
                    ->formatStateUsing(function (bool $state): string {
                        if ($state) {
                            return '<span class="poster-status poster-status--live"><span class="poster-status__dot"></span>Live</span>';
                        }

                        return '<span class="poster-status poster-status--draft"><span class="poster-status__dot"></span>Draft</span>';
                    })
                    ->html()
                    ->sortable()
                    ->alignCenter(),
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon(Heroicon::Star)
                    ->falseIcon(Heroicon::OutlinedStar)
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->alignCenter(),
                TextColumn::make('published_at')
                    ->label('Published')
                    ->formatStateUsing(fn (?Carbon $state): string => self::formatDateCell($state))
                    ->html()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->formatStateUsing(fn (?Carbon $state): string => self::formatDateCell($state))
                    ->html()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_published')
                    ->label('Status')
                    ->placeholder('All')
                    ->options([
                        '1' => 'Published',
                        '0' => 'Draft',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! filled($data['value'])) {
                            return $query;
                        }

                        return $query->where('is_published', $data['value'] === '1');
                    }),
                SelectFilter::make('category')
                    ->label('Category')
                    ->placeholder('All')
                    ->options(array_combine(Poster::CATEGORIES, Poster::CATEGORIES)),
                SelectFilter::make('is_featured')
                    ->label('Featured')
                    ->placeholder('All')
                    ->options([
                        '1' => 'Featured only',
                        '0' => 'Not featured',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! filled($data['value'])) {
                            return $query;
                        }

                        return $query->where('is_featured', $data['value'] === '1');
                    }),
                SelectFilter::make('date_order')
                    ->label('Date')
                    ->placeholder('Latest')
                    ->selectablePlaceholder(false)
                    ->default('latest')
                    ->options([
                        'latest' => 'Latest published',
                        'oldest' => 'Oldest published',
                        'updated' => 'Recently updated',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? 'latest') {
                            'oldest' => $query->orderBy('published_at', 'asc'),
                            'updated' => $query->orderBy('updated_at', 'desc'),
                            default => $query->orderBy('published_at', 'desc'),
                        };
                    }),
            ])
            ->recordActions([
                PortfolioTableActions::resourceGroup(
                    PosterResource::class,
                    'poster',
                    fn (Poster $record): bool => PosterResource::canView($record),
                    fn (Poster $record): bool => PosterResource::canEdit($record),
                    fn (Poster $record): string => $record->title,
                    fn (Poster $record) => view('filament.partials.poster-record-modal', PortfolioModalRows::poster($record)),
                    fn (Poster $record): string => PosterResource::getUrl('edit', ['record' => $record]),
                    'Delete this poster?',
                    'This poster will be removed from the public homepage.',
                    viewModalHeading: 'View details',
                    viewModalWidth: Width::FiveExtraLarge,
                    editActionLabel: 'Edit Post',
                ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(false)
                        ->extraAttributes(PortfolioEditorActions::sweetConfirmAttributes(
                            'Delete selected posters?',
                            'All selected posters will be removed permanently.',
                        ))
                        ->successNotification(null)
                        ->after(fn ($livewire): mixed => $livewire->dispatch(
                            'swal',
                            type: 'success',
                            title: 'Posters deleted',
                            text: 'Selected posters have been removed.',
                        )),
                ])->label('Bulk Actions'),
            ]);
    }

    private static function formatDateCell(?Carbon $state): string
    {
        if ($state === null) {
            return '<span class="poster-date poster-date--empty">—</span>';
        }

        return sprintf(
            '<span class="poster-date"><span class="poster-date__day">%s</span><span class="poster-date__time">%s</span></span>',
            e($state->format('M j, Y')),
            e($state->format('g:i A')),
        );
    }
}
