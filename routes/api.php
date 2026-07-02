<?php

use App\Http\Controllers\Api\V1\ContactMessageController;
use App\Http\Controllers\Api\V1\PublicContentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/case-studies', [PublicContentController::class, 'caseStudies']);
    Route::get('/posters', [PublicContentController::class, 'posters']);
    Route::get('/posters/featured', [PublicContentController::class, 'postersFeatured']);
    Route::get('/posters/{slug}', [PublicContentController::class, 'posterBySlug']);
    Route::get('/site', [PublicContentController::class, 'siteBundle']);
    Route::get('/contact/challenge', [ContactMessageController::class, 'challenge'])
        ->middleware('throttle:30,1');
    Route::post('/contact/messages', [ContactMessageController::class, 'store'])
        ->middleware('throttle:6,1');
});
