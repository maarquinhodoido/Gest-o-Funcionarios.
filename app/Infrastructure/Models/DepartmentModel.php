<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentModel extends Model
{
    use SoftDeletes;

    protected $table = 'departments';

    protected $fillable = [
        'reference',
        'company_id',
        'name',
        'description',
        'manager_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(CompanyModel::class, 'company_id');
    }

    public function manager()
    {
        return $this->belongsTo(UserModel::class, 'manager_id');
    }

    public function users()
    {
        return $this->hasMany(UserModel::class, 'department_id');
    }
}
