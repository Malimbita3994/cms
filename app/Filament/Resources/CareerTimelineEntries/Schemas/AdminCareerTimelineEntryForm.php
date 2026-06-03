<?php

namespace App\Filament\Resources\CareerTimelineEntries\Schemas;

use App\Filament\Support\PortfolioFormFields;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class AdminCareerTimelineEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'sm' => 1,
                'md' => 1,
                'lg' => 1,
                'xl' => 1,
                '2xl' => 1,
            ])
            ->statePath('data')
            ->components([
                Section::make('Career journey entry')
                    ->description('Shown on the homepage career timeline (year, role, and summary).')
                    ->extraAttributes(['class' => 'home-editor-card home-editor-card--main portfolio-stacked-editor-form'])
                    ->schema([
                        Grid::make()
                            ->columns(1)
                            ->schema([
                                Grid::make()
                                    ->columns(['default' => 1, 'lg' => 2])
                                    ->schema([
                                        TextInput::make('sort_order')
                                            ->label('Sort order')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Lower numbers appear first.'),
                                        TextInput::make('period_label')
                                            ->label('Year / period')
                                            ->required()
                                            ->maxLength(64)
                                            ->placeholder('e.g. 2022 – Present'),
                                        TextInput::make('title')
                                            ->label('Role / title')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                    ]),
                                PortfolioFormFields::richEditor('description', 'Summary', 'Summarize responsibilities and impact for this role…')
                                    ->helperText('Shown on the homepage career timeline and /career page.'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
