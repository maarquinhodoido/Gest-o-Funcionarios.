<?php

namespace App\Presentation\Controllers\API;

use App\Application\Services\RoleService;
use App\Presentation\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        private RoleService $roleService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $roles = $this->roleService->getAllRoles($companyId);
        return response()->json(['data' => $roles]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $role = $this->roleService->createRole($validated['name'], $companyId);

        return response()->json(['data' => $role], 201);
    }

    public function show(int $id): JsonResponse
    {
        $role = \Spatie\Permission\Models\Role::findById($id);
        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }
        return response()->json(['data' => $role]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $role = \Spatie\Permission\Models\Role::findById($id);
        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:roles,name,' . $id,
        ]);

        if (isset($validated['name'])) {
            $role->name = $validated['name'];
        }
        $role->save();

        return response()->json(['data' => $role]);
    }

    public function permissions(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $permissions = $this->roleService->getAllPermissions($companyId);
        return response()->json(['data' => $permissions]);
    }

    public function storePermission(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'module' => 'required|string|max:255',
            'action' => 'required|string|max:255',
        ]);

        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $permission = $this->roleService->createPermission(
            $validated['name'],
            $validated['module'],
            $validated['action'],
            $companyId,
        );

        return response()->json(['data' => $permission], 201);
    }

    public function assignPermission(Request $request, int $roleId): JsonResponse
    {
        $validated = $request->validate([
            'permission_id' => 'required|integer|exists:permissions,id',
        ]);

        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $this->roleService->assignPermissionToRole($roleId, $validated['permission_id'], $companyId);

        return response()->json(['message' => 'Permission assigned to role']);
    }

    public function removePermission(Request $request, int $roleId): JsonResponse
    {
        $validated = $request->validate([
            'permission_id' => 'required|integer|exists:permissions,id',
        ]);

        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $this->roleService->removePermissionFromRole($roleId, $validated['permission_id'], $companyId);

        return response()->json(['message' => 'Permission removed from role']);
    }

    public function rolePermissions(int $roleId): JsonResponse
    {
        $permissions = $this->roleService->getRolePermissions($roleId);
        return response()->json(['data' => $permissions]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->roleService->deleteRole($id);
        return response()->json(['message' => 'Role deleted']);
    }
}
