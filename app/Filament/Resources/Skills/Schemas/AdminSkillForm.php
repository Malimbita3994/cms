<?php

namespace App\Filament\Resources\Skills\Schemas;

use App\Filament\Support\PortfolioFormFields;
use App\Support\SkillContentItems;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class AdminSkillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->statePath('data')
            ->components([
                Section::make()
                    ->extraAttributes(['class' => 'home-editor-card home-editor-card--main portfolio-stacked-editor-form'])
                    ->schema([
                        Grid::make()
                            ->columns(1)
                            ->schema([
                                Grid::make()
                                    ->columns(['default' => 1, 'lg' => 2])
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Name')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('level')
                                            ->label('Proficiency (%)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->required()
                                            ->default(80),
                                    ]),
                                Select::make('icon')
                                    ->label('Icon (fallback when no image)')
                                    ->options(SkillContentItems::iconOptions())
                                    ->required()
                                    ->native(false),
                                PortfolioFormFields::imageUpload(
                                    'image',
                                    'uploads/skills',
                                    'Skill image',
                                    180,
                                    'Optional. Shown on skill cards and detail pages.',
                                    required: false,
                                )
                                    ->columnSpanFull(),
                                PortfolioFormFields::richEditor('focus', 'Description', 'Describe this skill…')
                                    ->helperText('Supports formatting, text alignment, lists, and links.'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
