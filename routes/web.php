<?php

use App\Http\Controllers\WelcomeLoginController;
use App\Http\Controllers\WelcomePasswordResetController;
use App\Models\CaseStudy;
use App\Models\Insight;
use App\Models\PortfolioProject;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Support\PortfolioAsset;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

Route::post('/login', [WelcomeLoginController::class, 'store'])
    ->middleware(['web', 'guest'])
    ->name('welcome.login');

Route::post('/password-reset', [WelcomePasswordResetController::class, 'store'])
    ->middleware(['web', 'guest'])
    ->name('welcome.password-reset');

Route::middleware('web')
    ->get('/admin/password-reset/request', function () {
        if (auth()->check()) {
            return redirect('/admin');
        }

        return redirect('/?forgot=1');
    })
    ->name('filament.admin.auth.password-reset.request');

Route::get('/media/{path}', function (string $path): BinaryFileResponse {
    $path = ltrim(str_replace(['..', '\\'], ['', '/'], $path), '/');
    $disk = Storage::disk(PortfolioAsset::DISK);

    abort_unless($path !== '' && $disk->exists($path), 404);

    return response()->file($disk->path($path));
})->where('path', '.*');

Route::get('/', function () {
    $appName = config('app.name', 'Malimbita');
    $stats = [
        'projects' => 0,
        'services' => 0,
        'case_studies' => 0,
        'insights' => 0,
    ];

    try {
        if (Schema::hasTable('site_settings')) {
            $settings = SiteSetting::query()->first();
            $appName = $settings?->app_name ?? $appName;
        }

        if (Schema::hasTable('portfolio_projects')) {
            $stats['projects'] = PortfolioProject::query()->count();
        }
        if (Schema::hasTable('services')) {
            $stats['services'] = Service::query()->count();
        }
        if (Schema::hasTable('case_studies')) {
            $stats['case_studies'] = CaseStudy::query()->count();
        }
        if (Schema::hasTable('insights')) {
            $stats['insights'] = Insight::query()->count();
        }
    } catch (\Throwable) {
        // Database unavailable — render landing page with safe defaults.
    }

    $errorBag = session('errors');
    $forgotBag = $errorBag ? $errorBag->getBag('forgot_password') : null;
    $openForgotModal = request()->boolean('forgot')
        || session()->has('forgot_password_status')
        || ($forgotBag && $forgotBag->any());
    $openLoginModal = ! $openForgotModal && (
        request()->boolean('login')
        || ($errorBag && $errorBag->getBag('default')->any())
    );

    return view('welcome', [
        'appName' => $appName,
        'stats' => $stats,
        'openLoginModal' => $openLoginModal,
        'openForgotModal' => $openForgotModal,
    ]);
});
