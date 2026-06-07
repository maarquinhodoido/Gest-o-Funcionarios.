<?php

namespace App\Presentation\Controllers\API;

use App\Application\Services\AuditService;
use App\Application\Services\NotificationService;
use App\Domain\Entities\AuditLog;
use App\Presentation\Controllers\Controller;
use App\Domain\Repositories\DepartmentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(
        private DepartmentRepositoryInterface $departmentRepository,
        private AuditService $auditService,
        private NotificationService $notificationService,
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
            reference: \App\Application\Services\ReferenceGenerator::generate('department'),
            companyId: $companyId,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            managerId: $validated['manager_id'] ?? null,
        );

        $saved = $this->departmentRepository->save($department);

        $this->auditService->log(
            companyId: $companyId,
            action: AuditLog::ACTION_CREATE,
            entityType: 'department',
            entityId: $saved->getId(),
            description: "Department created: {$saved->getName()}",
        );

        $this->notificationService->create(
            companyId: $companyId,
            title: 'Departamento Criado',
            message: "Departamento {$saved->getName()} foi criado",
            type: \App\Domain\Entities\Notification::TYPE_SUCCESS,
        );

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

        $updated = new \App\Domain\Entities\Department(
            id: $department->getId(),
            reference: $department->getReference(),
            companyId: $department->getCompanyId(),
            name: $validated['name'] ?? $department->getName(),
            description: $validated['description'] ?? $department->getDescription(),
            managerId: $validated['manager_id'] ?? $department->getManagerId(),
            isActive: $department->isActive(),
            createdAt: $department->getCreatedAt(),
            updatedAt: $department->getUpdatedAt(),
        );

        $updated = $this->departmentRepository->update($updated);

        $this->auditService->log(
            companyId: $updated->getCompanyId(),
            action: AuditLog::ACTION_UPDATE,
            entityType: 'department',
            entityId: $id,
            description: "Department updated: {$updated->getName()}",
        );

        $this->notificationService->create(
            companyId: $updated->getCompanyId(),
            title: 'Departamento Atualizado',
            message: "Departamento {$updated->getName()} foi atualizado",
            type: \App\Domain\Entities\Notification::TYPE_INFO,
        );

        return response()->json(['data' => $updated]);
    }

    public function destroy(int $id): JsonResponse
    {
        $department = $this->departmentRepository->findById($id);

        $this->departmentRepository->delete($id);

        if ($department) {
            $this->auditService->log(
                companyId: $department->getCompanyId(),
                action: AuditLog::ACTION_DELETE,
                entityType: 'department',
                entityId: $id,
                description: "Department deleted: {$department->getName()}",
            );

            $this->notificationService->create(
                companyId: $department->getCompanyId(),
                title: 'Departamento Eliminado',
                message: "Departamento {$department->getName()} foi eliminado",
                type: \App\Domain\Entities\Notification::TYPE_WARNING,
            );
        }

        return response()->json(['message' => 'Department deleted']);
    }
}
