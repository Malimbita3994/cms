<?php

namespace App\Filament\Resources\Posters;

use App\Filament\Resources\Posters\Pages\CreatePoster;
use App\Filament\Resources\Posters\Pages\EditPoster;
use App\Filament\Resources\Posters\Pages\ListPosters;
use App\Filament\Resources\Posters\Pages\ViewPoster;
use App\Filament\Resources\Posters\Schemas\AdminPosterForm;
use App\Filament\Resources\Posters\Tables\PostersTable;
use App\Filament\Concerns\AuthorizesResourceAccess;
use App\Filament\Support\NavigationGroups;
use App\Models\Poster;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PosterResource extends Resource
{
    use AuthorizesResourceAccess;

    protected static ?string $model = Poster::class;

    protected static ?string $navigationLabel = 'Posters / News';

    protected static ?string $modelLabel = 'Poster';

    protected static ?string $pluralModelLabel = 'Posters / News';

    protected static ?int $navigationSort = 10;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::NEWS;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static ?string $slug = 'posters';

    public static function form(Schema $schema): Schema
    {
        return AdminPosterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosters::route('/'),
            'create' => CreatePoster::route('/create'),
            'view' => ViewPoster::route('/{record}'),
            'edit' => EditPoster::route('/{record}/edit'),
        ];
    }
}
