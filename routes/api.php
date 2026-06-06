<?php

use Illuminate\Support\Facades\Route;
use App\Presentation\Controllers\API\AuthController;
use App\Presentation\Controllers\API\CompanyController;
use App\Presentation\Controllers\API\UserController;
use App\Presentation\Controllers\API\EquipmentController;
use App\Presentation\Controllers\API\EquipmentAssignmentController;
use App\Presentation\Controllers\API\AuditController;
use App\Presentation\Controllers\API\DashboardController;
use App\Presentation\Controllers\API\RoleController;
use App\Presentation\Controllers\API\OnboardingController;
use App\Presentation\Controllers\API\NotificationController;
use App\Presentation\Controllers\API\DepartmentController;
use App\Presentation\Controllers\API\EmployeeProfileController;

Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);

    Route::middleware(['auth:api', 'company.context'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::get('dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('dashboard/recent-activities', [DashboardController::class, 'recentActivities']);
        Route::get('dashboard/recent-logins', [DashboardController::class, 'recentLogins']);

        Route::apiResource('companies', CompanyController::class);
        Route::get('users/search', [UserController::class, 'search']);
        Route::post('users/{id}/block', [UserController::class, 'block']);
        Route::post('users/{id}/unblock', [UserController::class, 'unblock']);
        Route::post('users/{id}/reset-password', [UserController::class, 'resetPassword']);
        Route::apiResource('users', UserController::class);

        Route::apiResource('departments', DepartmentController::class);

        Route::get('employee-profiles/search', [EmployeeProfileController::class, 'search']);
        Route::apiResource('employee-profiles', EmployeeProfileController::class);

        Route::get('equipment/stats', [EquipmentController::class, 'stats']);
        Route::get('equipment/available', [EquipmentController::class, 'available']);
        Route::post('equipment/{id}/maintenance', [EquipmentController::class, 'markMaintenance']);
        Route::post('equipment/{id}/lost', [EquipmentController::class, 'markLost']);
        Route::apiResource('equipment', EquipmentController::class);

        Route::post('assignments/{id}/return', [EquipmentAssignmentController::class, 'return']);
        Route::post('assignments/{id}/lost', [EquipmentAssignmentController::class, 'markLost']);
        Route::get('assignments/user/{userId}', [EquipmentAssignmentController::class, 'userAssignments']);
        Route::get('assignments/equipment/{equipmentId}/history', [EquipmentAssignmentController::class, 'equipmentHistory']);
        Route::apiResource('assignments', EquipmentAssignmentController::class);

        Route::get('roles/permissions', [RoleController::class, 'permissions']);
        Route::post('roles/permissions', [RoleController::class, 'storePermission']);
        Route::post('roles/{roleId}/permissions/assign', [RoleController::class, 'assignPermission']);
        Route::post('roles/{roleId}/permissions/remove', [RoleController::class, 'removePermission']);
        Route::get('roles/{roleId}/permissions', [RoleController::class, 'rolePermissions']);
        Route::apiResource('roles', RoleController::class);

        Route::post('onboarding/onboard', [OnboardingController::class, 'onboard']);
        Route::post('onboarding/{userId}/offboard', [OnboardingController::class, 'offboard']);

        Route::get('audit/recent', [AuditController::class, 'recent']);
        Route::get('audit/suspicious', [AuditController::class, 'suspicious']);
        Route::get('audit/failed-logins', [AuditController::class, 'failedLogins']);
        Route::get('audit', [AuditController::class, 'index']);

        Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::get('notifications', [NotificationController::class, 'index']);
    });
});
