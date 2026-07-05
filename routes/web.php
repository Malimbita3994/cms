<?php

use App\Http\Controllers\WelcomeLoginController;
use App\Support\PortfolioAsset;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

Route::redirect('/admin/login', '/');
Route::redirect('/admin/password-reset/request', '/admin/password-reset');

Route::post('/login', [WelcomeLoginController::class, 'store'])
    ->middleware(['web', 'guest'])
    ->name('welcome.login');

Route::get('/media/{path}', function (string $path): BinaryFileResponse {
    $path = ltrim(str_replace(['..', '\\'], ['', '/'], $path), '/');
    $disk = Storage::disk(PortfolioAsset::DISK);

    abort_unless($path !== '' && $disk->exists($path), 404);

    return response()->file($disk->path($path));
})->where('path', '.*');

Route::get('/uploads/{path}', function (string $path): BinaryFileResponse {
    $path = ltrim(str_replace(['..', '\\'], ['', '/'], $path), '/');
    $storagePath = str_starts_with($path, 'uploads/') ? $path : 'uploads/'.$path;
    $disk = Storage::disk(PortfolioAsset::DISK);

    abort_unless($storagePath !== '' && $disk->exists($storagePath), 404);

    return response()->file($disk->path($storagePath));
})->where('path', '.*');

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/admin');
    }

    return view('admin-login');
});
