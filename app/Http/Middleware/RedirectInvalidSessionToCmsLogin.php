<?php

namespace App\Http\Middleware;

use App\Support\CmsAuth;
use Filament\Http\Middleware\AuthenticateSession as FilamentAuthenticateSession;

class RedirectInvalidSessionToCmsLogin extends FilamentAuthenticateSession
{
    protected function redirectTo($request): ?string
    {
        return CmsAuth::loginUrl();
    }
}
