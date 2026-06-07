<?php

namespace App\Presentation\Controllers\API;

use App\Application\DTOs\CreateEquipmentDTO;
use App\Application\Services\EquipmentService;
use App\Presentation\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function __construct(
        private EquipmentService $equipmentService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $filters = $request->only(['status', 'equipment_type_id', 'search', 'per_page']);
        $result = $this->equipmentService->findAll($companyId, $filters);

        return response()->json($result);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'equipment_type_id' => 'required|integer|exists:equipment_types,id',
            'serial_number' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'warranty_end' => 'nullable|date',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $dto = new CreateEquipmentDTO(
            companyId: $request->get('company_id', auth('api')->user()->company_id),
            equipmentTypeId: $validated['equipment_type_id'],
            serialNumber: $validated['serial_number'],
            brand: $validated['brand'],
            model: $validated['model'],
            location: $validated['location'] ?? null,
            warrantyEnd: $validated['warranty_end'] ?? null,
            purchaseDate: $validated['purchase_date'] ?? null,
            purchasePrice: $validated['purchase_price'] ?? null,
            supplier: $validated['supplier'] ?? null,
            notes: $validated['notes'] ?? null,
        );

        $equipment = $this->equipmentService->create($dto);

        return response()->json(['data' => $equipment], 201);
    }

    public function show(int $id): JsonResponse
    {
        $equipment = $this->equipmentService->findById($id);
        if (!$equipment) {
            return response()->json(['error' => 'Equipment not found'], 404);
        }
        return response()->json(['data' => $equipment]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'supplier' => 'nullable|string|max:255',
        ]);

        $equipment = $this->equipmentService->update($id, $validated);
        return response()->json(['data' => $equipment]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->equipmentService->delete($id);
        return response()->json(['message' => 'Equipment deleted']);
    }

    public function markMaintenance(int $id): JsonResponse
    {
        $equipment = $this->equipmentService->markMaintenance($id);
        return response()->json(['data' => $equipment]);
    }

    public function markAvailable(int $id): JsonResponse
    {
        $equipment = $this->equipmentService->markAvailable($id);
        return response()->json(['data' => $equipment]);
    }

    public function markLost(int $id): JsonResponse
    {
        $equipment = $this->equipmentService->markLost($id);
        return response()->json(['data' => $equipment]);
    }

    public function stats(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $stats = $this->equipmentService->getStats($companyId);
        return response()->json(['data' => $stats]);
    }

    public function available(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $equipment = $this->equipmentService->findAvailable($companyId);
        return response()->json(['data' => $equipment]);
    }
}
