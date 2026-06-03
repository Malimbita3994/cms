<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Support\PortfolioFormFields;
use App\Support\ServiceContentItems;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class AdminServiceForm
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
                                TextInput::make('title')
                                    ->label('Title')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('tagline')
                                    ->label('Tagline')
                                    ->maxLength(255)
                                    ->placeholder('One short line for the service detail page header')
                                    ->helperText('Shown under the title on /services pages. Keep the full story in Description below.'),
                                Select::make('icon')
                                    ->label('Icon (fallback when no image)')
                                    ->options(ServiceContentItems::iconOptions())
                                    ->required()
                                    ->native(false),
                                PortfolioFormFields::imageUpload(
                                    'image',
                                    'uploads/services',
                                    'Service image',
                                    180,
                                    'Optional. Shown on homepage service cards and /services.',
                                    required: false,
                                )
                                    ->columnSpanFull(),
                                PortfolioFormFields::richEditor('description', 'Description', 'Describe this service…')
                                    ->helperText('Full copy on the service detail page (next to the image). Homepage cards use the tagline when set.'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
