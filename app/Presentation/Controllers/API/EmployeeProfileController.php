<?php

namespace App\Presentation\Controllers\API;

use App\Application\Services\AuditService;
use App\Application\Services\NotificationService;
use App\Domain\Entities\AuditLog;
use App\Domain\Repositories\EmployeeProfileRepositoryInterface;
use App\Domain\Entities\EmployeeProfile;
use App\Presentation\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeProfileController extends Controller
{
    public function __construct(
        private EmployeeProfileRepositoryInterface $profileRepository,
        private AuditService $auditService,
        private NotificationService $notificationService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $filters = $request->only(['status', 'search', 'per_page']);
        $result = $this->profileRepository->findAll($companyId, $filters);

        return response()->json($result);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nif' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'niss' => 'nullable|string|max:20',
            'document_type' => 'nullable|string|in:BI,AR',
            'document_number' => 'nullable|string|max:50',
            'document_issue_date' => 'nullable|date',
            'document_expiry_date' => 'nullable|date',
            'position' => 'nullable|string|max:100',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        $profile = new EmployeeProfile(
            id: null,
            companyId: $request->get('company_id', auth('api')->user()->company_id),
            name: $validated['name'],
            reference: \App\Application\Services\ReferenceGenerator::generate('employee_profile'),
            nif: $validated['nif'] ?? null,
            birthDate: !empty($validated['birth_date']) ? new \DateTimeImmutable($validated['birth_date']) : null,
            phone: $validated['phone'] ?? null,
            niss: $validated['niss'] ?? null,
            documentType: $validated['document_type'] ?? null,
            documentNumber: $validated['document_number'] ?? null,
            documentIssueDate: !empty($validated['document_issue_date']) ? new \DateTimeImmutable($validated['document_issue_date']) : null,
            documentExpiryDate: !empty($validated['document_expiry_date']) ? new \DateTimeImmutable($validated['document_expiry_date']) : null,
            position: $validated['position'] ?? null,
            status: $validated['status'] ?? 'active',
        );

        $saved = $this->profileRepository->save($profile);

        $this->auditService->log(
            companyId: $saved->getCompanyId(),
            action: AuditLog::ACTION_CREATE,
            entityType: 'employee_profile',
            entityId: $saved->getId(),
            description: "Employee profile created: {$saved->getName()}",
        );

        $this->notificationService->create(
            companyId: $saved->getCompanyId(),
            title: 'Perfil Criado',
            message: "Perfil {$saved->getName()} foi criado",
            type: \App\Domain\Entities\Notification::TYPE_SUCCESS,
        );

        return response()->json(['data' => $saved], 201);
    }

    public function show(int $id): JsonResponse
    {
        $profile = $this->profileRepository->findById($id);
        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }
        return response()->json(['data' => $profile]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $profile = $this->profileRepository->findById($id);
        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'nif' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'niss' => 'nullable|string|max:20',
            'document_type' => 'nullable|string|in:BI,AR',
            'document_number' => 'nullable|string|max:50',
            'document_issue_date' => 'nullable|date',
            'document_expiry_date' => 'nullable|date',
            'position' => 'nullable|string|max:100',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        $updated = new EmployeeProfile(
            id: $profile->getId(),
            companyId: $profile->getCompanyId(),
            name: $validated['name'] ?? $profile->getName(),
            nif: $validated['nif'] ?? $profile->getNif(),
            birthDate: !empty($validated['birth_date']) ? new \DateTimeImmutable($validated['birth_date']) : $profile->getBirthDate(),
            phone: $validated['phone'] ?? $profile->getPhone(),
            niss: $validated['niss'] ?? $profile->getNiss(),
            documentType: $validated['document_type'] ?? $profile->getDocumentType(),
            documentNumber: $validated['document_number'] ?? $profile->getDocumentNumber(),
            documentIssueDate: !empty($validated['document_issue_date']) ? new \DateTimeImmutable($validated['document_issue_date']) : $profile->getDocumentIssueDate(),
            documentExpiryDate: !empty($validated['document_expiry_date']) ? new \DateTimeImmutable($validated['document_expiry_date']) : $profile->getDocumentExpiryDate(),
            position: $validated['position'] ?? $profile->getPosition(),
            status: $validated['status'] ?? $profile->getStatus(),
        );

        $saved = $this->profileRepository->update($updated);

        $this->auditService->log(
            companyId: $saved->getCompanyId(),
            action: AuditLog::ACTION_UPDATE,
            entityType: 'employee_profile',
            entityId: $id,
            description: "Employee profile updated: {$saved->getName()}",
        );

        $this->notificationService->create(
            companyId: $saved->getCompanyId(),
            title: 'Perfil Atualizado',
            message: "Perfil {$saved->getName()} foi atualizado",
            type: \App\Domain\Entities\Notification::TYPE_INFO,
        );

        return response()->json(['data' => $saved]);
    }

    public function destroy(int $id): JsonResponse
    {
        $profile = $this->profileRepository->findById($id);
        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $this->auditService->log(
            companyId: $profile->getCompanyId(),
            action: AuditLog::ACTION_DELETE,
            entityType: 'employee_profile',
            entityId: $id,
                description: "Employee profile deleted: {$profile->getName()}",
        );

        $this->notificationService->create(
            companyId: $profile->getCompanyId(),
            title: 'Perfil Eliminado',
            message: "Perfil {$profile->getName()} foi eliminado",
            type: \App\Domain\Entities\Notification::TYPE_WARNING,
        );

        $this->profileRepository->delete($id);
        return response()->json(['message' => 'Profile deleted']);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $profiles = $this->profileRepository->search($request->q, $companyId);
        return response()->json(['data' => $profiles]);
    }
}
