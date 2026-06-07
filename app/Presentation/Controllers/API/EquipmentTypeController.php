<?php

namespace App\Presentation\Controllers\API;

use App\Infrastructure\Models\EquipmentTypeModel;
use App\Presentation\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EquipmentTypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $types = EquipmentTypeModel::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json(['data' => $types]);
    }
}
