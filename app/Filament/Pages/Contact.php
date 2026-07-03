<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesPageAccess;
use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Pages\Tables\ContactMessagesTable;
use App\Filament\Support\NavigationGroups;
use App\Models\ContactMessage;
use App\Models\Profile;
use BackedEnum;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema as SchemaFacade;

class Contact extends Page implements HasActions, HasForms, HasTable
{
    use AuthorizesPageAccess;
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithPortfolioEditor;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Contact';

    protected static ?int $navigationSort = 40;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::SITE_PAGES;

    protected static ?string $slug = 'contact';

    protected string $view = 'filament.pages.contact';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $profile = Profile::query()->first();

        if ($profile) {
            $this->form->fill($profile->only([
                'email',
                'phone',
                'location',
                'linkedin_url',
                'github_url',
            ]));
        }
    }

    public function table(Table $table): Table
    {
        if (! SchemaFacade::hasTable('contact_messages')) {
            return $table->query(ContactMessage::query()->whereRaw('0 = 1'));
        }

        return ContactMessagesTable::configure($table);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Public contact details')
                    ->description('Email, phone, location, and social links shown on the public contact page.')
                    ->extraAttributes(['class' => 'home-editor-card home-editor-card--main home-editor-card--contact-details'])
                    ->schema([
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel(),
                        TextInput::make('location')
                            ->label('Location'),
                        TextInput::make('linkedin_url')
                            ->label('LinkedIn URL')
                            ->url(),
                        TextInput::make('github_url')
                            ->label('GitHub URL')
                            ->url(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $this->swalLoading('Saving contact details…');

        $profile = Profile::query()->first();

        if (! $profile) {
            $this->swalError(
                'Create an About profile first',
                'Add your profile under About before saving contact details.',
            );

            return;
        }

        $profile->update($this->form->getState());

        $this->queueSiteRevalidation();

        $this->swalSuccess(
            'Contact details saved',
            'Contact information has been updated on the live site.',
        );
    }

    public function cancel(): void
    {
        $this->redirect(filament()->getUrl());
    }

    public function getHeader(): ?View
    {
        return null;
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return null;
    }
}
