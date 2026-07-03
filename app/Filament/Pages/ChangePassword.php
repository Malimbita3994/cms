<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\InteractsWithPortfolioEditor;
use App\Filament\Support\NavigationGroups;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithPortfolioEditor;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $navigationLabel = 'Change Password';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 40;

    protected static string|\UnitEnum|null $navigationGroup = NavigationGroups::ACCESS_CONTROL;

    protected string $view = 'filament.pages.change-password';

    /** @var array{current_password?: string, password?: string, password_confirmation?: string} */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function getHeader(): ?View
    {
        return null;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Verify identity')
                    ->description('Confirm it is you before setting a new password.')
                    ->icon(Heroicon::OutlinedKey)
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->autocomplete('current-password'),
                    ]),
                Section::make('New password')
                    ->description('Must be at least 8 characters.')
                    ->icon(Heroicon::OutlinedLockClosed)
                    ->schema([
                        TextInput::make('password')
                            ->label('New password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->confirmed()
                            ->autocomplete('new-password'),
                        TextInput::make('password_confirmation')
                            ->label('Confirm new password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->autocomplete('new-password'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $this->swalLoading('Updating password…');

        $user = auth()->user();

        if (! $user) {
            return;
        }

        $validated = $this->form->getState();

        Validator::make($validated, [
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ])->validate();

        if (! Hash::check($validated['current_password'], $user->password)) {
            $this->addError('data.current_password', 'The current password is incorrect.');
            $this->swalError('Incorrect password', 'The current password you entered is wrong.');

            return;
        }

        $user->update([
            'password' => $validated['password'],
        ]);

        $this->form->fill();

        $this->swalSuccess('Password changed', 'Your password has been updated successfully.');
    }
}
