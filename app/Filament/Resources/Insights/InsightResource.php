<?php

namespace App\Filament\Resources\Insights;

use App\Filament\Resources\Insights\Pages\CreateInsight;
use App\Filament\Resources\Insights\Pages\EditInsight;
use App\Filament\Resources\Insights\Pages\ListInsights;
use App\Filament\Resources\Insights\Pages\ViewInsight;
use App\Filament\Resources\Insights\Schemas\InsightForm;
use App\Filament\Resources\Insights\Tables\InsightsTable;
use App\Filament\Concerns\AuthorizesResourceAccess;
use App\Filament\Support\NavigationGroups;
use App\Models\Insight;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InsightResource extends Resource
{
    use AuthorizesResourceAccess;

    protected static ?string $model = Insight::class;

    protected static ?string $navigationLabel = 'Insights';

    protected static ?int $navigationSort = 50;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::PORTFOLIO;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    protected static ?string $slug = 'insights';

    public static function form(Schema $schema): Schema
    {
        return InsightForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InsightsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInsights::route('/'),
            'create' => CreateInsight::route('/create'),
            'view' => ViewInsight::route('/{record}'),
            'edit' => EditInsight::route('/{record}/edit'),
        ];
    }
}
