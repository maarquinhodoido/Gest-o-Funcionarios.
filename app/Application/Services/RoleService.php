<?php

namespace App\Application\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Domain\Entities\AuditLog;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function __construct(
        private AuditService $auditService,
    ) {}

    public function createRole(string $name, int $companyId): Role
    {
        $role = Role::create([
            'name' => $name,
            'guard_name' => 'api',
        ]);

        $this->auditService->log(
            companyId: $companyId,
            action: AuditLog::ACTION_CREATE,
            entityType: 'role',
            entityId: $role->id,
            description: "Role created: {$name}",
        );

        return $role;
    }

    public function createPermission(string $name, string $module, string $action, int $companyId): Permission
    {
        $permission = Permission::create([
            'name' => "{$module}.{$action}",
            'guard_name' => 'api',
            'module' => $module,
            'action' => $action,
            'company_id' => $companyId,
        ]);

        $this->auditService->log(
            companyId: $companyId,
            action: AuditLog::ACTION_CREATE,
            entityType: 'permission',
            entityId: $permission->id,
            description: "Permission created: {$module}.{$action}",
        );

        return $permission;
    }

    public function assignPermissionToRole(int $roleId, int $permissionId, int $companyId): void
    {
        $role = Role::findById($roleId);
        $permission = Permission::findById($permissionId);
        $role->givePermissionTo($permission);

        $this->auditService->log(
            companyId: $companyId,
            action: AuditLog::ACTION_PERMISSION_CHANGE,
            entityType: 'role_permission',
            description: "Permission '{$permission->name}' assigned to role '{$role->name}'",
        );
    }

    public function removePermissionFromRole(int $roleId, int $permissionId, int $companyId): void
    {
        $role = Role::findById($roleId);
        $permission = Permission::findById($permissionId);
        $role->revokePermissionTo($permission);

        $this->auditService->log(
            companyId: $companyId,
            action: AuditLog::ACTION_PERMISSION_CHANGE,
            entityType: 'role_permission',
            description: "Permission '{$permission->name}' removed from role '{$role->name}'",
        );
    }

    public function assignRoleToUser(int $userId, string $roleName, int $companyId): void
    {
        $user = app(\App\Infrastructure\Models\UserModel::class)->find($userId);
        $role = Role::findByName($roleName);
        $user->assignRole($role);

        $this->auditService->log(
            companyId: $companyId,
            userId: $userId,
            action: AuditLog::ACTION_PERMISSION_CHANGE,
            entityType: 'user_role',
            description: "Role '{$roleName}' assigned to user #{$userId}",
        );
    }

    public function getAllRoles(int $companyId): array
    {
        return Role::all()->toArray();
    }

    public function getAllPermissions(int $companyId): array
    {
        return Permission::all()->toArray();
    }

    public function getRolePermissions(int $roleId): array
    {
        $role = Role::findById($roleId);
        return $role->permissions->toArray();
    }

    public function deleteRole(int $id): void
    {
        $role = Role::findById($id);
        $role->delete();
    }
}
