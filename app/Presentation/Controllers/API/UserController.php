<?php

namespace App\Presentation\Controllers\API;

use App\Application\DTOs\CreateUserDTO;
use App\Application\DTOs\UpdateUserDTO;
use App\Application\Services\UserService;
use App\Presentation\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $filters = $request->only(['status', 'department_id', 'search', 'per_page']);
        $result = $this->userService->findAll($companyId, $filters);

        $items = array_map(function ($user) {
            $data = $user->jsonSerialize();
            $model = \App\Infrastructure\Models\UserModel::find($user->getId());
            $data['roles'] = $model ? $model->getRoleNames()->toArray() : [];
            return $data;
        }, $result['items']);

        $result['items'] = $items;
        return response()->json($result);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'employee_profile_id' => 'nullable|integer|exists:employee_profiles,id',
            'department_id' => 'nullable|integer|exists:departments,id',
            'position_id' => 'nullable|integer',
            'hire_date' => 'nullable|date',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $dto = new CreateUserDTO(
            reference: null,
            name: $validated['name'],
            email: $validated['email'],
            password: $validated['password'],
            companyId: $request->get('company_id', auth('api')->user()->company_id),
            phone: $validated['phone'] ?? null,
            employeeProfileId: $validated['employee_profile_id'] ?? null,
            departmentId: $validated['department_id'] ?? null,
            positionId: $validated['position_id'] ?? null,
            hireDate: $validated['hire_date'] ?? null,
            roles: $validated['roles'] ?? [],
        );

        $user = $this->userService->create($dto);

        return response()->json(['data' => $this->userWithRoles($user)], 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->userService->findById($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        return response()->json(['data' => $this->userWithRoles($user)]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'employee_profile_id' => 'nullable|integer|exists:employee_profiles,id',
            'department_id' => 'nullable|integer|exists:departments,id',
            'position_id' => 'nullable|integer',
            'hire_date' => 'nullable|date',
            'status' => 'nullable|string|in:active,blocked,inactive',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $dto = new UpdateUserDTO(
            id: $id,
            name: $validated['name'] ?? null,
            email: $validated['email'] ?? null,
            phone: $validated['phone'] ?? null,
            employeeProfileId: $validated['employee_profile_id'] ?? null,
            departmentId: $validated['department_id'] ?? null,
            positionId: $validated['position_id'] ?? null,
            hireDate: $validated['hire_date'] ?? null,
            status: $validated['status'] ?? null,
            roles: $validated['roles'] ?? [],
        );
        $user = $this->userService->update($dto);

        return response()->json(['data' => $this->userWithRoles($user)]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->userService->delete($id);
        return response()->json(['message' => 'User deleted']);
    }

    public function block(int $id): JsonResponse
    {
        $user = $this->userService->block($id);
        return response()->json(['data' => $user]);
    }

    public function unblock(int $id): JsonResponse
    {
        $user = $this->userService->unblock($id);
        return response()->json(['data' => $user]);
    }

    public function resetPassword(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user = $this->userService->resetPassword($id, $validated['password']);
        return response()->json(['message' => 'Password reset successfully']);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);
        $companyId = $request->get('company_id', auth('api')->user()->company_id);
        $users = $this->userService->search($request->q, $companyId);
        return response()->json(['data' => $users]);
    }

    private function userWithRoles(\App\Domain\Entities\User $user): array
    {
        $data = $user->jsonSerialize();
        $model = \App\Infrastructure\Models\UserModel::find($user->getId());
        $data['roles'] = $model ? $model->getRoleNames()->toArray() : [];
        return $data;
    }
}
