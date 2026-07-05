<?php

namespace App\Filament\Auth\Http\Responses;

use App\Support\CmsAuth;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        session()->flash('auth_alert', [
            'type' => 'success',
            'title' => 'Signed out',
            'text' => 'You have been logged out safely. See you next time.',
        ]);

        return redirect()->to(CmsAuth::loginUrl());
    }
}
