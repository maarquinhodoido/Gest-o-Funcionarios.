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

        $userNotifs = $this->notificationService->findByUser($userId, ['per_page' => 999]);
        $companyNotifs = $this->notificationService->findByCompany(auth('api')->user()->company_id, ['per_page' => 999, 'user_id_null' => true]);

        $all = array_merge($userNotifs['items'] ?? [], $companyNotifs['items'] ?? []);
        usort($all, fn($a, $b) => $b->getCreatedAt()->getTimestamp() - $a->getCreatedAt()->getTimestamp());
        $all = array_slice($all, 0, $filters['per_page'] ?? 20);

        return response()->json(['data' => $all]);
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
