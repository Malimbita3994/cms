<?php

namespace App\Filament\Resources\PortfolioProjects\Schemas;

use App\Filament\Support\LineDelimitedArray;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PortfolioProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                LineDelimitedArray::textarea('technologies', 'Technologies'),
                LineDelimitedArray::textarea('achievements', 'Achievements'),
                TextInput::make('image')
                    ->required()
                    ->helperText('Public path served by Next.js, e.g. /projects/dashboard.svg'),
                TextInput::make('preview')
                    ->required(),
            ]);
    }
}
