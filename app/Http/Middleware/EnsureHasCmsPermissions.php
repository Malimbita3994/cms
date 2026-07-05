<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\CmsAuth;
use App\Support\FilamentPermissions;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasCmsPermissions
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(Filament::getAuthGuard()) ?? Filament::auth()->user();

        if ($user instanceof User) {
            if ($request->session()->get('cms_permissions_verified') !== $user->id) {
                if (! FilamentPermissions::hasAnyCmsPermission($user)) {
                    Filament::auth()->logout();

                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->to(CmsAuth::loginUrl())
                        ->with('auth_alert', [
                            'type' => 'error',
                            'title' => 'No access',
                            'text' => 'Your account has no permissions assigned. Ask an administrator to assign you a role.',
                        ]);
                }

                $request->session()->put('cms_permissions_verified', $user->id);
            }
        }

        return $next($request);
    }
}
