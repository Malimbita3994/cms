<?php

namespace App\Filament\Resources\PortfolioProjects;

use App\Filament\Concerns\AuthorizesResourceAccess;
use App\Filament\Support\NavigationGroups;
use App\Filament\Resources\PortfolioProjects\Pages\CreatePortfolioProject;
use App\Filament\Resources\PortfolioProjects\Pages\EditPortfolioProject;
use App\Filament\Resources\PortfolioProjects\Pages\ListPortfolioProjects;
use App\Filament\Resources\PortfolioProjects\Pages\ViewPortfolioProject;
use App\Filament\Resources\PortfolioProjects\Schemas\PortfolioProjectForm;
use App\Filament\Resources\PortfolioProjects\Tables\PortfolioProjectsTable;
use App\Models\PortfolioProject;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PortfolioProjectResource extends Resource
{
    use AuthorizesResourceAccess;

    protected static ?string $model = PortfolioProject::class;

    protected static ?string $navigationLabel = 'Projects';

    protected static ?int $navigationSort = 5;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::CONTENT;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static ?string $slug = 'portfolio-projects';

    public static function form(Schema $schema): Schema
    {
        return PortfolioProjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PortfolioProjectsTable::configure($table);
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
            'index' => ListPortfolioProjects::route('/'),
            'create' => CreatePortfolioProject::route('/create'),
            'view' => ViewPortfolioProject::route('/{record}'),
            'edit' => EditPortfolioProject::route('/{record}/edit'),
        ];
    }
}
