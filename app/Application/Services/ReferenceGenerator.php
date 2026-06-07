<?php

namespace App\Application\Services;

use Illuminate\Support\Facades\DB;

class ReferenceGenerator
{
    const PREFIXES = [
        'company' => 'EMP',
        'user' => 'USR',
        'equipment' => 'EQ',
        'department' => 'DEP',
        'role' => 'ROLE',
        'employee_profile' => 'FUNC',
        'equipment_assignment' => 'ATRIB',
    ];

    const TABLE_COLUMNS = [
        'company' => ['table' => 'companies', 'column' => 'reference'],
        'user' => ['table' => 'users', 'column' => 'reference'],
        'equipment' => ['table' => 'equipment', 'column' => 'reference'],
        'department' => ['table' => 'departments', 'column' => 'reference'],
        'employee_profile' => ['table' => 'employee_profiles', 'column' => 'reference'],
        'equipment_assignment' => ['table' => 'equipment_assignments', 'column' => 'reference'],
    ];

    public static function generate(string $entityType): string
    {
        $prefix = self::PREFIXES[$entityType] ?? 'REF';
        $config = self::TABLE_COLUMNS[$entityType] ?? null;

        if (!$config) {
            return $prefix . '-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        }

        $last = DB::table($config['table'])
            ->where($config['column'], 'like', $prefix . '-%')
            ->orderBy($config['column'], 'desc')
            ->value($config['column']);

        if ($last) {
            $num = (int) substr($last, strlen($prefix) + 1);
            $num++;
        } else {
            $num = 1;
        }

        return $prefix . '-' . str_pad((string) $num, 5, '0', STR_PAD_LEFT);
    }
}
