<?php

namespace App\Filament\Auth\Http\Responses;

use App\Services\AdminNotificationService;
use App\Support\FilamentPermissions;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as Responsable;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $user = Filament::auth()->user();

        session()->flash('auth_alert', [
            'type' => 'success',
            'title' => 'Welcome back',
            'text' => $user
                ? "Signed in as {$user->name}. Your dashboard is ready."
                : 'You have signed in successfully.',
        ]);

        $homeUrl = FilamentPermissions::resolvePanelHomeUrl();

        if ($user) {
            app(AdminNotificationService::class)->recordSystem(
                'Signed in',
                "{$user->name} signed in to the admin panel.",
                $homeUrl,
                'auth',
            );
        }

        return redirect()->intended($homeUrl);
    }
}
