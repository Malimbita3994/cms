<?php

namespace App\Http\Middleware;

use App\Support\CmsAuth;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;

class RedirectGuestsToCmsLogin extends FilamentAuthenticate
{
    protected function redirectTo($request): ?string
    {
        return CmsAuth::loginUrl();
    }
}
