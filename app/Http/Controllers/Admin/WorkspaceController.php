<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminNotificationService;
use App\Support\AdminGlobalSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function search(Request $request, AdminGlobalSearch $search): JsonResponse
    {
        $query = (string) $request->string('q');

        try {
            return response()->json($search->search($query));
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'query' => $query,
                'groups' => [],
                'took_ms' => 0,
                'error' => 'Search failed. Please try again.',
            ], 500);
        }
    }

    public function notifications(Request $request, AdminNotificationService $notifications): JsonResponse
    {
        $user = $request->user();

        abort_unless($user, 403);

        return response()->json($notifications->forUser($user));
    }

    public function notificationsSummary(Request $request, AdminNotificationService $notifications): JsonResponse
    {
        $user = $request->user();

        abort_unless($user, 403);

        return response()->json([
            'unread_count' => $notifications->unreadCountForUser($user),
        ]);
    }

    public function markNotificationRead(Request $request, AdminNotificationService $notifications): JsonResponse
    {
        $user = $request->user();

        abort_unless($user, 403);

        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:admin_notifications,id'],
        ]);

        $notifications->markRead($user, (int) $validated['id']);

        return response()->json([
            'ok' => true,
            'unread_count' => $notifications->unreadCountForUser($user),
        ]);
    }

    public function markAllNotificationsRead(Request $request, AdminNotificationService $notifications): JsonResponse
    {
        $user = $request->user();

        abort_unless($user, 403);

        $notifications->markAllRead($user);

        return response()->json([
            'ok' => true,
            'unread_count' => 0,
        ]);
    }
}
