<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyModel extends Model
{
    use SoftDeletes;

    protected $table = 'companies';

    protected $fillable = [
        'reference',
        'name',
        'legal_name',
        'tax_id',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'plan',
        'status',
        'max_users',
        'logo',
        'primary_color',
        'trial_ends_at',
        'subscribed_at',
        'subscription_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscribed_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'max_users' => 'integer',
    ];

    public function users()
    {
        return $this->hasMany(UserModel::class, 'company_id');
    }

    public function equipment()
    {
        return $this->hasMany(EquipmentModel::class, 'company_id');
    }

    public function departments()
    {
        return $this->hasMany(DepartmentModel::class, 'company_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLogModel::class, 'company_id');
    }
}
