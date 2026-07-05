<?php

namespace App\Filament\Auth;

use App\Support\CmsAuth;
use Filament\Actions\Action;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Illuminate\Contracts\Support\Htmlable;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    protected static string $layout = 'filament.layout.auth-simple';

    public function hasLogo(): bool
    {
        return false;
    }

    public function loginAction(): Action
    {
        return parent::loginAction()
            ->label('Back to login')
            ->url(CmsAuth::loginUrl());
    }

    public function getSubheading(): string | Htmlable | null
    {
        return $this->loginAction;
    }
}
