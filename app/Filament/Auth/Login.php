<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use App\Support\FilamentPermissions;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected static string $layout = 'filament.layout.auth';

    protected static ?string $navigationLabel = null;

    public function hasLogo(): bool
    {
        return false;
    }

    public function getHeading(): string | Htmlable | null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getHeading();
        }

        return 'Welcome back';
    }

    public function getSubheading(): string | Htmlable | null
    {
        if (filled($this->userUndertakingMultiFactorAuthentication)) {
            return parent::getSubheading();
        }

        return 'Sign in to manage your portfolio content, projects, and site settings.';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                Group::make([
                    $this->getRememberFormComponent(),
                    View::make('filament.auth.forgot-password'),
                ])->extraAttributes(['class' => 'mb-auth-actions-row']),
            ]);
    }

    protected function getEmailFormComponent(): Component
    {
        return parent::getEmailFormComponent()
            ->placeholder('admin@example.local')
            ->prefixIcon(Heroicon::OutlinedEnvelope, true)
            ->prefixIconColor('gray')
            ->inlinePrefix()
            ->extraAttributes(['class' => 'mb-auth-field']);
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->hint(null)
            ->placeholder('Enter your password')
            ->prefixIcon(Heroicon::OutlinedLockClosed, true)
            ->prefixIconColor('gray')
            ->inlinePrefix()
            ->extraAttributes(['class' => 'mb-auth-field']);
    }

    protected function getRememberFormComponent(): Component
    {
        return parent::getRememberFormComponent()
            ->extraAttributes(['class' => 'mb-auth-remember']);
    }

    protected function getAuthenticateFormAction(): \Filament\Actions\Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Sign in to admin')
            ->icon(Heroicon::OutlinedArrowRightEndOnRectangle)
            ->iconPosition(IconPosition::Before);
    }

    public function authenticate(): ?\Filament\Auth\Http\Responses\Contracts\LoginResponse
    {
        $response = parent::authenticate();

        $user = Filament::auth()->user();

        if ($user && ! $user->isActive()) {
            Filament::auth()->logout();

            throw ValidationException::withMessages([
                'data.email' => 'This account is inactive. Contact an administrator.',
            ]);
        }

        if ($user && ! FilamentPermissions::hasAnyCmsPermission($user)) {
            Filament::auth()->logout();

            throw ValidationException::withMessages([
                'data.email' => 'Your account has no permissions assigned. Ask an administrator to assign you a role.',
            ]);
        }

        if ($user) {
            $user->forceFill(['last_login_at' => now()])->saveQuietly();
        }

        return $response;
    }
}
