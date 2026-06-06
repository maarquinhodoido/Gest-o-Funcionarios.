<?php

namespace App\Presentation\Controllers\API;

use App\Application\Services\DashboardService;
use App\Presentation\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
    ) {}

    public function stats(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $stats = $this->dashboardService->getStats($companyId);

        return response()->json(['data' => $stats]);
    }

    public function recentActivities(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $limit = $request->get('limit', 10);
        $activities = $this->dashboardService->getRecentActivities($companyId, $limit);

        return response()->json(['data' => $activities]);
    }

    public function recentLogins(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $limit = $request->get('limit', 10);
        $logins = $this->dashboardService->getRecentLogins($companyId, $limit);

        return response()->json(['data' => $logins]);
    }
}
