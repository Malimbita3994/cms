<?php

namespace App\Http\Controllers;

use Filament\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use LogicException;

class WelcomePasswordResetController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $panel = Filament::getPanel('admin');

        $status = Password::broker(Filament::getAuthPasswordBroker())->sendResetLink(
            ['email' => $validated['email']],
            function (CanResetPassword $user, string $token) use ($panel): void {
                if (
                    ($user instanceof FilamentUser) &&
                    (! $user->canAccessPanel($panel))
                ) {
                    return;
                }

                if (! method_exists($user, 'notify')) {
                    $userClass = $user::class;

                    throw new LogicException("Model [{$userClass}] does not have a [notify()] method.");
                }

                $notification = app(ResetPasswordNotification::class, ['token' => $token]);
                $notification->url = $panel->getResetPasswordUrl($token, $user);

                $user->notify($notification);

                if (class_exists(PasswordResetLinkSent::class)) {
                    event(new PasswordResetLinkSent($user));
                }
            },
        );

        if ($status === Password::RESET_THROTTLED) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ])->errorBag('forgot_password');
        }

        return redirect('/?forgot=1')->with(
            'forgot_password_status',
            'If an account exists for that email, we have sent a password reset link.',
        );
    }
}
