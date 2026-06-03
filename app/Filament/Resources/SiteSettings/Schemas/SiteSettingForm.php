<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('app_name')
                    ->required(),
                TextInput::make('site_title')
                    ->required(),
                Textarea::make('site_description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('site_url')
                    ->url()
                    ->required()
                    ->default('https://example.com'),
            ]);
    }
}
