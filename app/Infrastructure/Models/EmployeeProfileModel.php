<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeProfileModel extends Model
{
    use SoftDeletes;

    protected $table = 'employee_profiles';

    protected $fillable = [
        'company_id',
        'name',
        'nif',
        'birth_date',
        'phone',
        'niss',
        'position',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(CompanyModel::class, 'company_id');
    }

    public function user()
    {
        return $this->hasOne(UserModel::class, 'employee_profile_id');
    }
}
