<?php

use App\Http\Controllers\Admin\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])
    ->prefix('admin/api/workspace')
    ->name('admin.workspace.')
    ->group(function (): void {
        Route::get('/search', [WorkspaceController::class, 'search'])->name('search');
        Route::get('/notifications', [WorkspaceController::class, 'notifications'])->name('notifications');
        Route::get('/notifications/summary', [WorkspaceController::class, 'notificationsSummary'])->name('notifications.summary');
        Route::post('/notifications/read', [WorkspaceController::class, 'markNotificationRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [WorkspaceController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
    });
