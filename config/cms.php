<?php

return [
    'site_api_cache_seconds' => (int) env('CMS_SITE_API_CACHE_SECONDS', 300),
    'notification_cache_seconds' => (int) env('CMS_NOTIFICATION_CACHE_SECONDS', 60),
    'dashboard_cache_seconds' => (int) env('CMS_DASHBOARD_CACHE_SECONDS', 300),
];
