<?php

namespace App\Presentation\Controllers\API;

use App\Application\DTOs\CreateCompanyDTO;
use App\Application\Services\CompanyService;
use App\Presentation\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(
        private CompanyService $companyService,
    ) {}

    public function index(): JsonResponse
    {
        $companies = $this->companyService->findAll();
        return response()->json(['data' => $companies]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'legal_name' => 'required|string|max:255',
            'tax_id' => 'required|string|max:50|unique:companies,tax_id',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'plan' => 'nullable|string|in:free,basic,professional,enterprise',
            'max_users' => 'nullable|integer|min:1|max:9999',
        ]);

        $dto = new CreateCompanyDTO(
            name: $validated['name'],
            legalName: $validated['legal_name'],
            taxId: $validated['tax_id'],
            email: $validated['email'] ?? null,
            phone: $validated['phone'] ?? null,
            address: $validated['address'] ?? null,
            city: $validated['city'] ?? null,
            state: $validated['state'] ?? null,
            country: $validated['country'] ?? null,
            postalCode: $validated['postal_code'] ?? null,
            plan: $validated['plan'] ?? 'free',
            maxUsers: $validated['max_users'] ?? 10,
        );
        $company = $this->companyService->create($dto);

        return response()->json(['data' => $company], 201);
    }

    public function show(int $id): JsonResponse
    {
        $company = $this->companyService->findById($id);
        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }
        return response()->json(['data' => $company]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'plan' => 'nullable|string|in:free,basic,professional,enterprise',
        ]);

        $company = $this->companyService->update($id, $validated);
        return response()->json(['data' => $company]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->companyService->delete($id);
        return response()->json(['message' => 'Company deleted']);
    }
}
