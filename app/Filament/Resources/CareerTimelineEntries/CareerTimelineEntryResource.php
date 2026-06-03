<?php

namespace App\Filament\Resources\CareerTimelineEntries;

use App\Filament\Concerns\AuthorizesResourceAccess;
use App\Filament\Support\NavigationGroups;
use App\Filament\Resources\CareerTimelineEntries\Pages\CreateCareerTimelineEntry;
use App\Filament\Resources\CareerTimelineEntries\Pages\EditCareerTimelineEntry;
use App\Filament\Resources\CareerTimelineEntries\Pages\ListCareerTimelineEntries;
use App\Filament\Resources\CareerTimelineEntries\Schemas\AdminCareerTimelineEntryForm;
use App\Filament\Resources\CareerTimelineEntries\Tables\CareerTimelineEntriesTable;
use App\Models\CareerTimelineEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CareerTimelineEntryResource extends Resource
{
    use AuthorizesResourceAccess;

    protected static ?string $model = CareerTimelineEntry::class;

    protected static ?string $navigationLabel = 'Career Journey';

    protected static ?string $modelLabel = 'career journey entry';

    protected static ?string $pluralModelLabel = 'career journey entries';

    protected static ?int $navigationSort = 4;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::CONTENT;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $slug = 'career-journey';

    public static function form(Schema $schema): Schema
    {
        return AdminCareerTimelineEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CareerTimelineEntriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCareerTimelineEntries::route('/'),
            'create' => CreateCareerTimelineEntry::route('/create'),
            'edit' => EditCareerTimelineEntry::route('/{record}/edit'),
        ];
    }
}
