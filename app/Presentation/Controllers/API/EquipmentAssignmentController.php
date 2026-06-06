<?php

namespace App\Presentation\Controllers\API;

use App\Application\DTOs\AssignEquipmentDTO;
use App\Application\Services\EquipmentAssignmentService;
use App\Presentation\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EquipmentAssignmentController extends Controller
{
    public function __construct(
        private EquipmentAssignmentService $assignmentService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $filters = $request->only(['status', 'user_id', 'per_page']);
        $result = $this->assignmentService->findAllByCompany($companyId, $filters);

        return response()->json($result);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'equipment_id' => 'required|integer|exists:equipment,id',
            'user_id' => 'required|integer|exists:users,id',
            'notes' => 'nullable|string',
            'expected_return_at' => 'nullable|date',
            'responsibility_term' => 'nullable|string',
        ]);

        $dto = new AssignEquipmentDTO(
            equipmentId: $validated['equipment_id'],
            userId: $validated['user_id'],
            assignedBy: auth('api')->id(),
            notes: $validated['notes'] ?? null,
            expectedReturnAt: $validated['expected_return_at'] ?? null,
            responsibilityTerm: $validated['responsibility_term'] ?? null,
        );

        $assignment = $this->assignmentService->assign($dto);

        return response()->json(['data' => $assignment], 201);
    }

    public function show(int $id): JsonResponse
    {
        $assignment = $this->assignmentService->findById($id);
        if (!$assignment) {
            return response()->json(['error' => 'Assignment not found'], 404);
        }
        return response()->json(['data' => $assignment]);
    }

    public function return(int $id): JsonResponse
    {
        $assignment = $this->assignmentService->return($id, auth('api')->id());
        return response()->json(['data' => $assignment]);
    }

    public function markLost(int $id): JsonResponse
    {
        $assignment = $this->assignmentService->markLost($id);
        return response()->json(['data' => $assignment]);
    }

    public function userAssignments(int $userId): JsonResponse
    {
        $assignments = $this->assignmentService->findActiveByUser($userId);
        return response()->json(['data' => $assignments]);
    }

    public function equipmentHistory(int $equipmentId): JsonResponse
    {
        $history = $this->assignmentService->findHistoryByEquipment($equipmentId);
        return response()->json(['data' => $history]);
    }
}
