<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Filament\Resources\CaseStudies\CaseStudyResource;
use App\Models\CaseStudy;
use App\Models\ContactMessage;
use App\Models\Insight;
use App\Models\PortfolioProject;
use App\Models\Service;
use App\Models\Skill;
use App\Models\User;
use App\Support\DashboardMetrics;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use App\Support\AdminSchema;

final class AdminNotificationService
{
    /**
     * @return array{unread_count: int, items: array<int, array<string, mixed>>}
     */
    public function forUser(User $user, int $limit = 30): array
    {
        if (! AdminSchema::hasAdminNotifications()) {
            return ['unread_count' => 0, 'items' => []];
        }

        $seconds = (int) config('cms.notification_cache_seconds', 30);
        $cacheKey = "admin:notifications:bundle:{$user->id}:{$limit}";

        return Cache::remember($cacheKey, now()->addSeconds($seconds), function () use ($user, $limit): array {
            $this->ensureBackfill();

            $readIds = $user->adminNotificationReads()
                ->pluck('admin_notification_id')
                ->flip();

            $notifications = AdminNotification::query()
                ->latest('created_at')
                ->limit($limit)
                ->get();

            $items = $notifications->map(function (AdminNotification $notification) use ($readIds): array {
                $read = $readIds->has($notification->id);

                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'category' => $notification->category,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'url' => $notification->url,
                    'icon' => $notification->icon,
                    'read' => $read,
                    'when' => $notification->created_at?->diffForHumans() ?? 'just now',
                    'timestamp' => $notification->created_at?->timestamp ?? 0,
                ];
            })->all();

            return [
                'unread_count' => collect($items)->where('read', false)->count(),
                'items' => $items,
            ];
        });
    }

    public function unreadCountForUser(User $user): int
    {
        if (! AdminSchema::hasAdminNotifications()) {
            return 0;
        }

        $seconds = (int) config('cms.notification_cache_seconds', 30);
        $cacheKey = "admin:notifications:unread:{$user->id}";

        return (int) Cache::remember($cacheKey, now()->addSeconds($seconds), function () use ($user): int {
            $this->ensureBackfill();

            return (int) AdminNotification::query()
                ->whereNotExists(function ($query) use ($user): void {
                    $query->selectRaw('1')
                        ->from('admin_notification_reads')
                        ->whereColumn(
                            'admin_notification_reads.admin_notification_id',
                            'admin_notifications.id',
                        )
                        ->where('admin_notification_reads.user_id', $user->id);
                })
                ->count();
        });
    }

    public static function flushForUser(int $userId): void
    {
        Cache::forget("admin:notifications:unread:{$userId}");

        foreach ([30] as $limit) {
            Cache::forget("admin:notifications:bundle:{$userId}:{$limit}");
        }
    }

    public function markRead(User $user, int $notificationId): void
    {
        if (! AdminSchema::hasAdminNotificationReads()) {
            return;
        }

        $user->adminNotificationReads()->updateOrCreate(
            ['admin_notification_id' => $notificationId],
            ['read_at' => now()],
        );

        self::flushForUser((int) $user->id);
    }

    public function markAllRead(User $user): void
    {
        if (! AdminSchema::hasAdminNotifications()) {
            return;
        }

        $ids = AdminNotification::query()->pluck('id');

        foreach ($ids as $id) {
            $user->adminNotificationReads()->updateOrCreate(
                ['admin_notification_id' => $id],
                ['read_at' => now()],
            );
        }

        self::flushForUser((int) $user->id);
    }

    public function record(Model $model, string $type = 'updated'): ?AdminNotification
    {
        if (! AdminSchema::hasAdminNotifications()) {
            return null;
        }

        $payload = $this->payloadForModel($model, $type);

        if ($payload === null) {
            return null;
        }

        $existing = AdminNotification::query()
            ->where('subject_type', $model::class)
            ->where('subject_id', $model->getKey())
            ->where('type', $type)
            ->where('created_at', '>=', now()->subMinutes(2))
            ->first();

        if ($existing) {
            $existing->update([
                'title' => $payload['title'],
                'body' => $payload['body'],
                'url' => $payload['url'],
                'meta' => $payload['meta'],
            ]);

            return $existing;
        }

        return AdminNotification::query()->create([
            'type' => $type,
            'category' => $payload['category'],
            'title' => $payload['title'],
            'body' => $payload['body'],
            'url' => $payload['url'],
            'icon' => $payload['icon'],
            'subject_type' => $model::class,
            'subject_id' => $model->getKey(),
            'meta' => $payload['meta'],
        ]);
    }

    public function recordSystem(string $title, string $body, ?string $url = null, string $category = 'system'): AdminNotification
    {
        return AdminNotification::query()->create([
            'type' => 'system',
            'category' => $category,
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'icon' => 'bell',
        ]);
    }

    public function ensureBackfill(): void
    {
        if (! AdminSchema::hasAdminNotifications()) {
            return;
        }

        if (AdminNotification::query()->exists()) {
            return;
        }

        $this->backfillFromRecentContent();
    }

    public function backfillFromRecentContent(): void
    {
        $since = now()->subDays(14);

        $this->backfillModel(PortfolioProject::class, $since);
        $this->backfillModel(Insight::class, $since);
        $this->backfillModel(Service::class, $since);
        $this->backfillModel(Skill::class, $since);
        $this->backfillModel(CaseStudy::class, $since);

        DashboardMetrics::flush();
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    protected function backfillModel(string $modelClass, Carbon $since): void
    {
        if (! class_exists($modelClass)) {
            return;
        }

        $modelClass::query()
            ->where('updated_at', '>=', $since)
            ->orderByDesc('updated_at')
            ->limit(12)
            ->get()
            ->each(fn (Model $model) => $this->record($model, 'updated'));
    }

    /**
     * @return array{category: string, title: string, body: string|null, url: string|null, icon: string, meta: array<string, mixed>}|null
     */
    protected function payloadForModel(Model $model, string $type): ?array
    {
        return match ($model::class) {
            PortfolioProject::class => [
                'category' => 'project',
                'title' => $type === 'created' ? 'New project' : 'Project updated',
                'body' => (string) $model->title,
                'url' => url('/admin/portfolio-projects/'.$model->getKey().'/edit'),
                'icon' => 'folder',
                'meta' => ['record_id' => $model->getKey()],
            ],
            Insight::class => [
                'category' => 'insight',
                'title' => $type === 'created' ? 'New insight' : 'Insight updated',
                'body' => (string) $model->title,
                'url' => url('/admin/insights/'.$model->getKey().'/edit'),
                'icon' => 'document',
                'meta' => ['record_id' => $model->getKey()],
            ],
            Service::class => [
                'category' => 'service',
                'title' => $type === 'created' ? 'New service' : 'Service updated',
                'body' => (string) $model->title,
                'url' => url('/admin/services/'.$model->getKey().'/edit'),
                'icon' => 'cube',
                'meta' => ['record_id' => $model->getKey()],
            ],
            Skill::class => [
                'category' => 'skill',
                'title' => $type === 'created' ? 'New skill' : 'Skill updated',
                'body' => (string) ($model->name ?? 'Skill'),
                'url' => url('/admin/skills/'.$model->getKey().'/edit'),
                'icon' => 'academic',
                'meta' => ['record_id' => $model->getKey()],
            ],
            CaseStudy::class => [
                'category' => 'case_study',
                'title' => $type === 'created' ? 'New case study' : 'Case study updated',
                'body' => (string) ($model->title ?? 'Case study'),
                'url' => CaseStudyResource::getUrl('edit', ['record' => $model->getKey()]),
                'icon' => 'briefcase',
                'meta' => ['record_id' => $model->getKey()],
            ],
            ContactMessage::class => [
                'category' => 'contact',
                'title' => 'New contact message',
                'body' => $type === 'created'
                    ? trim((string) $model->name.' — '.str((string) $model->message)->limit(80))
                    : (string) $model->name,
                'url' => url('/admin/contact'),
                'icon' => 'envelope',
                'meta' => ['record_id' => $model->getKey()],
            ],
            default => null,
        };
    }
}
