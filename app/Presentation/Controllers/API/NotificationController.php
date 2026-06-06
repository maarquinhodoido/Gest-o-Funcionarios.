<?php

namespace App\Presentation\Controllers\API;

use App\Application\Services\NotificationService;
use App\Presentation\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userId = auth('api')->id();
        $filters = $request->only(['is_read', 'type', 'per_page']);
        $notifications = $this->notificationService->findByUser($userId, $filters);

        return response()->json($notifications);
    }

    public function unreadCount(): JsonResponse
    {
        $userId = auth('api')->id();
        $count = $this->notificationService->countUnread($userId);

        return response()->json(['data' => ['unread_count' => $count]]);
    }

    public function markAsRead(int $id): JsonResponse
    {
        $this->notificationService->markAsRead($id);
        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(): JsonResponse
    {
        $userId = auth('api')->id();
        $this->notificationService->markAllAsRead($userId);

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
