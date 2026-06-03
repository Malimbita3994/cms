<?php

namespace App\Observers;

use App\Services\AdminNotificationService;
use App\Support\DashboardMetrics;
use App\Support\SiteContentCache;
use Illuminate\Database\Eloquent\Model;

class ContentActivityObserver
{
    public function created(Model $model): void
    {
        app(AdminNotificationService::class)->record($model, 'created');
        DashboardMetrics::flush();
        SiteContentCache::flush();
    }

    public function updated(Model $model): void
    {
        if ($model->wasChanged() === false) {
            return;
        }

        app(AdminNotificationService::class)->record($model, 'updated');
        DashboardMetrics::flush();
        SiteContentCache::flush();
    }
}
