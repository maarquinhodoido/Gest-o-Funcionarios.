<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentTypeModel extends Model
{
    protected $table = 'equipment_types';

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(CompanyModel::class, 'company_id');
    }

    public function equipment()
    {
        return $this->hasMany(EquipmentModel::class, 'equipment_type_id');
    }
}
