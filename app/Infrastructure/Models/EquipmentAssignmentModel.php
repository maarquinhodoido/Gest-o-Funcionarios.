<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentAssignmentModel extends Model
{
    use SoftDeletes;

    protected $table = 'equipment_assignments';

    protected $fillable = [
        'reference',
        'equipment_id',
        'user_id',
        'assigned_by',
        'assigned_at',
        'returned_at',
        'returned_by',
        'status',
        'notes',
        'digital_signature',
        'responsibility_term',
        'expected_return_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
        'expected_return_at' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(EquipmentModel::class, 'equipment_id');
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(UserModel::class, 'assigned_by');
    }

    public function returnedBy()
    {
        return $this->belongsTo(UserModel::class, 'returned_by');
    }
}
