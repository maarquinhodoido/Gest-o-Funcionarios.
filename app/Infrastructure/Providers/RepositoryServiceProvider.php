<?php

namespace App\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Repositories\CompanyRepositoryInterface;
use App\Domain\Repositories\EquipmentRepositoryInterface;
use App\Domain\Repositories\EquipmentAssignmentRepositoryInterface;
use App\Domain\Repositories\AuditLogRepositoryInterface;
use App\Domain\Repositories\DepartmentRepositoryInterface;
use App\Domain\Repositories\NotificationRepositoryInterface;
use App\Domain\Repositories\EmployeeProfileRepositoryInterface;
use App\Infrastructure\Repositories\EloquentUserRepository;
use App\Infrastructure\Repositories\EloquentCompanyRepository;
use App\Infrastructure\Repositories\EloquentEquipmentRepository;
use App\Infrastructure\Repositories\EloquentEquipmentAssignmentRepository;
use App\Infrastructure\Repositories\EloquentAuditLogRepository;
use App\Infrastructure\Repositories\EloquentDepartmentRepository;
use App\Infrastructure\Repositories\EloquentNotificationRepository;
use App\Infrastructure\Repositories\EloquentEmployeeProfileRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, EloquentCompanyRepository::class);
        $this->app->bind(EquipmentRepositoryInterface::class, EloquentEquipmentRepository::class);
        $this->app->bind(EquipmentAssignmentRepositoryInterface::class, EloquentEquipmentAssignmentRepository::class);
        $this->app->bind(AuditLogRepositoryInterface::class, EloquentAuditLogRepository::class);
        $this->app->bind(DepartmentRepositoryInterface::class, EloquentDepartmentRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, EloquentNotificationRepository::class);
        $this->app->bind(EmployeeProfileRepositoryInterface::class, EloquentEmployeeProfileRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
