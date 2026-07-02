<?php

namespace App\Filament\Resources\Posters\Schemas;

use App\Filament\Support\PortfolioFormFields;
use App\Models\Poster;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

final class AdminPosterForm
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
                                        'uploads/posters',
                                        'Poster image',
                                        180,
                                        'Shown on the homepage and update detail page.',
                                        required: false,
                                    ),
                                    PortfolioFormFields::pdfUpload(
                                        'pdf',
                                        'uploads/posters/pdfs',
                                        'Attach PDF',
                                        'Optional PDF download linked from the public update page.',
                                        required: false,
                                    ),
                                ])
                                    ->extraAttributes(['class' => 'home-editor-col home-editor-col--left']),
                                Group::make([
                                    Hidden::make('title_for_slug')
                                        ->default('')
                                        ->dehydrated(false),
                                    TextInput::make('title')
                                        ->label('Title')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(debounce: 400)
                                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state): void {
                                            $previousTitle = (string) $get('title_for_slug');
                                            $currentSlug = (string) $get('slug');
                                            $previousSlug = Str::slug($previousTitle);

                                            if ($currentSlug === '' || $currentSlug === $previousSlug) {
                                                $set('slug', Str::slug($state ?? ''));
                                            }

                                            $set('title_for_slug', $state ?? '');
                                        }),
                                    TextInput::make('slug')
                                        ->label('URL slug')
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (Set $set, ?string $state): void {
                                            $set('slug', Str::slug($state ?? ''));
                                        })
                                        ->helperText('Updates automatically from the title. Edit this field anytime for a custom slug.'),
                                    Select::make('category')
                                        ->label('Category')
                                        ->required()
                                        ->options(array_combine(Poster::CATEGORIES, Poster::CATEGORIES))
                                        ->native(false),
                                ])
                                    ->extraAttributes(['class' => 'home-editor-col home-editor-col--right']),
                            ]),
                        PortfolioFormFields::richEditor(
                            'short_description',
                            'Short description',
                            'Brief summary for cards and homepage…',
                        )
                            ->columnSpanFull(),
                        PortfolioFormFields::richEditor(
                            'content',
                            'Full content',
                            'Write the full update, blog post, or announcement…',
                        )
                            ->columnSpanFull(),
                        Grid::make()
                            ->columns(['default' => 1, 'sm' => 2])
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Published on site')
                                    ->default(false)
                                    ->inline(false),
                                Toggle::make('is_featured')
                                    ->label('Featured on homepage')
                                    ->default(false)
                                    ->inline(false),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
