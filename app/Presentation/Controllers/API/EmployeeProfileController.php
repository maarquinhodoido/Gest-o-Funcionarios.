<?php

namespace App\Presentation\Controllers\API;

use App\Domain\Repositories\EmployeeProfileRepositoryInterface;
use App\Domain\Entities\EmployeeProfile;
use App\Presentation\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeProfileController extends Controller
{
    public function __construct(
        private EmployeeProfileRepositoryInterface $profileRepository,
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
            'position' => 'nullable|string|max:100',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        $profile = new EmployeeProfile(
            companyId: $request->get('company_id', auth('api')->user()->company_id),
            name: $validated['name'],
            nif: $validated['nif'] ?? null,
            birthDate: $validated['birth_date'] ? new \DateTimeImmutable($validated['birth_date']) : null,
            phone: $validated['phone'] ?? null,
            niss: $validated['niss'] ?? null,
            position: $validated['position'] ?? null,
            status: $validated['status'] ?? 'active',
        );

        $saved = $this->profileRepository->save($profile);

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
            'position' => 'nullable|string|max:100',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        $updated = new EmployeeProfile(
            id: $profile->getId(),
            companyId: $profile->getCompanyId(),
            name: $validated['name'] ?? $profile->getName(),
            nif: $validated['nif'] ?? $profile->getNif(),
            birthDate: isset($validated['birth_date']) ? new \DateTimeImmutable($validated['birth_date']) : $profile->getBirthDate(),
            phone: $validated['phone'] ?? $profile->getPhone(),
            niss: $validated['niss'] ?? $profile->getNiss(),
            position: $validated['position'] ?? $profile->getPosition(),
            status: $validated['status'] ?? $profile->getStatus(),
        );

        $saved = $this->profileRepository->update($updated);

        return response()->json(['data' => $saved]);
    }

    public function destroy(int $id): JsonResponse
    {
        $profile = $this->profileRepository->findById($id);
        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

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
