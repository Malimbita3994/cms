<?php

namespace App\Filament\Resources\Insights\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InsightForm
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
                Textarea::make('excerpt')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('display_date')
                    ->helperText('Shown as publication date, e.g. March 2026'),
                TextInput::make('image')
                    ->required()
                    ->helperText('Public path, e.g. /insights/government-apis.svg'),
            ]);
    }
}
