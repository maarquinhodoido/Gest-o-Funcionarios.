<?php

namespace App\Infrastructure\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserModel extends Authenticatable implements JWTSubject
{
    use HasFactory, HasRoles, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'reference',
        'name',
        'email',
        'password',
        'company_id',
        'employee_profile_id',
        'phone',
        'department_id',
        'position_id',
        'hire_date',
        'status',
        'is_online',
        'profile_photo',
        'two_factor_secret',
        'two_factor_enabled',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'hire_date' => 'date',
        'is_online' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'company_id' => $this->company_id,
        ];
    }

    public function company()
    {
        return $this->belongsTo(CompanyModel::class, 'company_id');
    }

    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfileModel::class, 'employee_profile_id');
    }

    public function department()
    {
        return $this->belongsTo(DepartmentModel::class, 'department_id');
    }

    public function activeAssignments()
    {
        return $this->hasMany(EquipmentAssignmentModel::class, 'user_id')
            ->where('status', 'active');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLogModel::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(NotificationModel::class, 'user_id');
    }
}
