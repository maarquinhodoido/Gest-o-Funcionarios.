<?php

namespace Database\Seeders;

use App\Infrastructure\Models\CompanyModel;
use App\Infrastructure\Models\UserModel;
use App\Infrastructure\Models\EquipmentTypeModel;
use App\Infrastructure\Models\DepartmentModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $company = CompanyModel::create([
            'name' => 'Empresa Demo',
            'legal_name' => 'Empresa Demo Ltda',
            'tax_id' => '00.000.000/0001-00',
            'email' => 'admin@empresademo.com',
            'phone' => '+351 900 000 000',
            'address' => 'Rua Principal, 1000',
            'city' => 'Lisboa',
            'state' => 'Lisboa',
            'country' => 'Portugal',
            'postal_code' => '1000-001',
            'plan' => 'enterprise',
            'status' => 'active',
            'max_users' => 100,
        ]);

        $admin = UserModel::create([
            'name' => 'Administrador',
            'email' => 'admin@empresademo.com',
            'password' => Hash::make('123'),
            'company_id' => $company->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('super-admin');

        DepartmentModel::create([
            'company_id' => $company->id,
            'name' => 'Tecnologia',
            'description' => 'Departamento de TI',
        ]);

        DepartmentModel::create([
            'company_id' => $company->id,
            'name' => 'Recursos Humanos',
            'description' => 'Departamento de RH',
        ]);

        DepartmentModel::create([
            'company_id' => $company->id,
            'name' => 'Financeiro',
            'description' => 'Departamento Financeiro',
        ]);

        $types = ['Computador', 'Portátil', 'Telemóvel', 'Impressora', 'Scanner', 'Tablet', 'Monitor', 'Licença'];
        foreach ($types as $type) {
            EquipmentTypeModel::create([
                'company_id' => $company->id,
                'name' => $type,
                'description' => "Tipo de equipamento: {$type}",
            ]);
        }
    }
}
