<?php

namespace App\Filament\Resources\CaseStudies;

use App\Filament\Concerns\AuthorizesResourceAccess;
use App\Filament\Support\NavigationGroups;
use App\Filament\Resources\CaseStudies\Pages\CreateCaseStudy;
use App\Filament\Resources\CaseStudies\Pages\EditCaseStudy;
use App\Filament\Resources\CaseStudies\Pages\ListCaseStudies;
use App\Filament\Resources\CaseStudies\Pages\ViewCaseStudy;
use App\Filament\Resources\CaseStudies\Schemas\AdminCaseStudyForm;
use App\Filament\Resources\CaseStudies\Tables\CaseStudiesTable;
use App\Models\CaseStudy;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CaseStudyResource extends Resource
{
    use AuthorizesResourceAccess;

    protected static ?string $model = CaseStudy::class;

    protected static ?string $navigationLabel = 'Case Studies';

    protected static ?string $modelLabel = 'case study';

    protected static ?string $pluralModelLabel = 'case studies';

    protected static ?int $navigationSort = 20;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::PORTFOLIO;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $slug = 'case-studies';

    public static function form(Schema $schema): Schema
    {
        return AdminCaseStudyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CaseStudiesTable::configure($table);
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
            'index' => ListCaseStudies::route('/'),
            'create' => CreateCaseStudy::route('/create'),
            'view' => ViewCaseStudy::route('/{record}'),
            'edit' => EditCaseStudy::route('/{record}/edit'),
        ];
    }

}
