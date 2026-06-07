<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentModel extends Model
{
    use SoftDeletes;

    protected $table = 'equipment';

    protected $fillable = [
        'reference',
        'company_id',
        'equipment_type_id',
        'serial_number',
        'brand',
        'model',
        'status',
        'location',
        'warranty_end',
        'purchase_date',
        'purchase_price',
        'supplier',
        'notes',
        'qr_code',
    ];

    protected $casts = [
        'warranty_end' => 'date',
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(CompanyModel::class, 'company_id');
    }

    public function type()
    {
        return $this->belongsTo(EquipmentTypeModel::class, 'equipment_type_id');
    }

    public function activeAssignment()
    {
        return $this->hasOne(EquipmentAssignmentModel::class, 'equipment_id')
            ->where('status', 'active');
    }

    public function assignments()
    {
        return $this->hasMany(EquipmentAssignmentModel::class, 'equipment_id');
    }
}
