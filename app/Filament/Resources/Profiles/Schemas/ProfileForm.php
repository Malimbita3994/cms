<?php

namespace App\Filament\Resources\Profiles\Schemas;

use App\Filament\Support\LineDelimitedArray;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('role')
                    ->required(),
                Textarea::make('tagline')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('summary')
                    ->required()
                    ->columnSpanFull(),
                LineDelimitedArray::textarea('strengths', 'Strengths'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('location'),
                TextInput::make('linkedin_url')
                    ->url(),
                TextInput::make('github_url')
                    ->url(),
                TextInput::make('image')
                    ->helperText('Path under Next.js public/, e.g. /profile-hd.png?v=7'),
            ]);
    }
}
