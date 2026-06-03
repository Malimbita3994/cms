<?php

namespace App\Filament\Resources\PortfolioProjects\Schemas;

use App\Filament\Support\PortfolioFormFields;
use App\Support\LineDelimitedText;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class AdminPortfolioProjectForm
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
                                        'uploads/projects',
                                        'Project image',
                                        160,
                                        'Main image on project cards and detail pages.',
                                    ),
                                    PortfolioFormFields::imageUpload(
                                        'preview',
                                        'uploads/projects/previews',
                                        'Card preview text image',
                                        120,
                                        'Short preview line shown on homepage project cards.',
                                        required: false,
                                    ),
                                ])
                                    ->extraAttributes(['class' => 'home-editor-col home-editor-col--left']),
                                Group::make([
                                    TextInput::make('title')
                                        ->label('Title')
                                        ->required()
                                        ->maxLength(255),
                                    PortfolioFormFields::richEditor('description', 'Description'),
                                    Textarea::make('technologies')
                                        ->label('Technologies')
                                        ->helperText('One item per line.')
                                        ->rows(3)
                                        ->formatStateUsing(fn ($state) => LineDelimitedText::toForm(is_array($state) ? $state : null))
                                        ->dehydrateStateUsing(fn (?string $state) => LineDelimitedText::toStorage($state)),
                                    Textarea::make('achievements')
                                        ->label('Achievements')
                                        ->helperText('One item per line.')
                                        ->rows(3)
                                        ->formatStateUsing(fn ($state) => LineDelimitedText::toForm(is_array($state) ? $state : null))
                                        ->dehydrateStateUsing(fn (?string $state) => LineDelimitedText::toStorage($state)),
                                ])
                                    ->extraAttributes(['class' => 'home-editor-col home-editor-col--right']),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
