<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

/**
 * Cached schema probes (avoid information_schema on every admin request).
 */
final class AdminSchema
{
    private static ?bool $hasAdminNotifications = null;

    private static ?bool $hasAdminNotificationReads = null;

    public static function hasAdminNotifications(): bool
    {
        return static::$hasAdminNotifications ??= Schema::hasTable('admin_notifications');
    }

    public static function hasAdminNotificationReads(): bool
    {
        return static::$hasAdminNotificationReads ??= Schema::hasTable('admin_notification_reads');
    }
}
