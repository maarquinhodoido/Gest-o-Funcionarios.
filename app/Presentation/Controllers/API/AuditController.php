<?php

namespace App\Presentation\Controllers\API;

use App\Application\Services\AuditService;
use App\Presentation\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function __construct(
        private AuditService $auditService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $filters = $request->only(['action', 'entity_type', 'severity', 'user_id', 'from', 'to', 'per_page']);
        $result = $this->auditService->findAll($companyId, $filters);

        return response()->json($result);
    }

    public function recent(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $limit = $request->get('limit', 20);
        $logs = $this->auditService->findRecent($companyId, $limit);

        return response()->json(['data' => $logs]);
    }

    public function suspicious(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $activities = $this->auditService->findSuspiciousActivities($companyId);

        return response()->json(['data' => $activities]);
    }

    public function failedLogins(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $minutes = $request->get('minutes', 30);
        $logs = $this->auditService->findFailedLogins($companyId, $minutes);

        return response()->json(['data' => $logs]);
    }
}
