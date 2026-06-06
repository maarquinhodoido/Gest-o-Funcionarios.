<?php

namespace App\Presentation\Controllers\API;

use App\Application\DTOs\CreateUserDTO;
use App\Application\Services\OnboardingService;
use App\Presentation\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function __construct(
        private OnboardingService $onboardingService,
    ) {}

    public function onboard(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'department_id' => 'nullable|integer|exists:departments,id',
            'position_id' => 'nullable|integer',
            'hire_date' => 'nullable|date',
            'equipment_ids' => 'nullable|array',
            'equipment_ids.*' => 'integer|exists:equipment,id',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $companyId = $request->get('company_id', auth('api')->user()->company_id);

        $dto = new CreateUserDTO(
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
            companyId: $companyId,
            phone: $validated['phone'] ?? null,
            departmentId: $validated['department_id'] ?? null,
            positionId: $validated['position_id'] ?? null,
            hireDate: $validated['hire_date'] ?? null,
        );

        $result = $this->onboardingService->onboard(
            dto: $dto,
            equipmentIds: $validated['equipment_ids'] ?? [],
            roleNames: $validated['roles'] ?? [],
        );

        return response()->json(['data' => $result], 201);
    }

    public function offboard(Request $request, int $userId): JsonResponse
    {
        $this->onboardingService->offboard($userId);
        return response()->json(['message' => 'User offboarded successfully']);
    }
}
