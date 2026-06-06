<?php

namespace App\Presentation\Controllers\API;

use App\Presentation\Controllers\Controller;
use App\Domain\Repositories\DepartmentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(
        private DepartmentRepositoryInterface $departmentRepository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $departments = $this->departmentRepository->findAll($companyId);

        return response()->json(['data' => $departments]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|integer|exists:users,id',
        ]);

        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $department = new \App\Domain\Entities\Department(
            companyId: $companyId,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            managerId: $validated['manager_id'] ?? null,
        );

        $saved = $this->departmentRepository->save($department);

        return response()->json(['data' => $saved], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $department = $this->departmentRepository->findById($id);
        if (!$department) {
            return response()->json(['error' => 'Department not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|integer|exists:users,id',
        ]);

        $updated = $this->departmentRepository->update($department);

        return response()->json(['data' => $updated]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->departmentRepository->delete($id);
        return response()->json(['message' => 'Department deleted']);
    }
}
