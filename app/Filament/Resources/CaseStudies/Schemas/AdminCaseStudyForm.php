<?php

namespace App\Filament\Resources\CaseStudies\Schemas;

use App\Filament\Support\LineDelimitedArray;
use App\Filament\Support\PortfolioFormFields;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class AdminCaseStudyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->statePath('data')
            ->components([
                Section::make('Case study')
                    ->description('Content appears in the Project case studies section on the public home page.')
                    ->extraAttributes(['class' => 'home-editor-card home-editor-card--main portfolio-stacked-editor-form'])
                    ->schema([
                        Grid::make()
                            ->columns(1)
                            ->schema([
                                Grid::make()
                                    ->columns(['default' => 1, 'lg' => 2])
                                    ->schema([
                                        TextInput::make('sort_order')
                                            ->label('Display order')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Lower numbers appear first on the site.'),
                                        TextInput::make('title')
                                            ->label('Title')
                                            ->required()
                                            ->maxLength(255),
                                        Toggle::make('is_published')
                                            ->label('Published on site')
                                            ->default(true)
                                            ->inline(false),
                                    ]),
                                PortfolioFormFields::imageUpload(
                                    'image',
                                    'uploads/case-studies',
                                    'Case study image',
                                    180,
                                    'Optional image shown on homepage and /case-studies cards.',
                                    required: false,
                                )
                                    ->columnSpanFull(),
                                PortfolioFormFields::richEditor('desc', 'Description', 'Describe the project and solution…')
                                    ->helperText('Shown as the main case study copy on the homepage.'),
                                PortfolioFormFields::richEditor('impact', 'Impact', 'Summarize measurable outcomes…')
                                    ->helperText('Displayed below the description (e.g. delivery improvements).'),
                                LineDelimitedArray::textarea('stack', 'Technology stack')
                                    ->helperText('One technology per line (e.g. Laravel, PostgreSQL).'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
