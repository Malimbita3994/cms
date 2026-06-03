<?php

namespace App\Filament\Resources\Insights\Schemas;

use App\Filament\Support\PortfolioFormFields;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class AdminInsightForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make()
                    ->extraAttributes(['class' => 'home-editor-card home-editor-card--main'])
                    ->schema([
                        Grid::make()
                            ->columns(['default' => 1, 'lg' => 2])
                            ->schema([
                                Group::make([
                                    PortfolioFormFields::imageUpload(
                                        'image',
                                        'uploads/insights',
                                        'Insight image',
                                        160,
                                        'Shown on insights list and detail cards.',
                                        required: false,
                                    ),
                                ])
                                    ->extraAttributes(['class' => 'home-editor-col home-editor-col--left']),
                                Group::make([
                                    TextInput::make('title')
                                        ->label('Title')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('display_date')
                                        ->label('Publication date')
                                        ->placeholder('March 2026')
                                        ->helperText('Displayed on the public insights list.'),
                                    PortfolioFormFields::richEditor('excerpt', 'Excerpt', 'Write the insight summary…'),
                                ])
                                    ->extraAttributes(['class' => 'home-editor-col home-editor-col--right']),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
