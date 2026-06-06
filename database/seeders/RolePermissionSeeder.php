<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdmin = Role::create(['name' => 'super-admin', 'guard_name' => 'api']);
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'api']);
        $manager = Role::create(['name' => 'manager', 'guard_name' => 'api']);
        $user = Role::create(['name' => 'user', 'guard_name' => 'api']);

        $modules = ['users', 'equipment', 'assignments', 'roles', 'departments', 'audit', 'companies'];
        $actions = ['view', 'create', 'edit', 'delete', 'approve'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::create([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'api',
                ]);
            }
        }

        $allPermissions = Permission::all();
        $superAdmin->syncPermissions($allPermissions);

        $adminPermissions = Permission::whereIn('name', [
            'users.view', 'users.create', 'users.edit',
            'equipment.view', 'equipment.create', 'equipment.edit',
            'assignments.view', 'assignments.create', 'assignments.edit',
            'departments.view', 'departments.create', 'departments.edit',
            'audit.view',
        ])->get();
        $admin->syncPermissions($adminPermissions);

        $managerPermissions = Permission::whereIn('name', [
            'users.view',
            'equipment.view', 'equipment.create',
            'assignments.view', 'assignments.create',
            'departments.view',
        ])->get();
        $manager->syncPermissions($managerPermissions);

        $userPermissions = Permission::whereIn('name', [
            'equipment.view',
            'assignments.view',
        ])->get();
        $user->syncPermissions($userPermissions);
    }
}
