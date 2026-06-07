<?php

namespace Database\Seeders;

use App\Infrastructure\Models\CompanyModel;
use App\Infrastructure\Models\UserModel;
use App\Infrastructure\Models\DepartmentModel;
use App\Infrastructure\Models\EquipmentTypeModel;
use App\Infrastructure\Models\EquipmentModel;
use App\Infrastructure\Models\EmployeeProfileModel;
use App\Infrastructure\Models\EquipmentAssignmentModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $company = CompanyModel::first();
        if (!$company) {
            $this->command->error('No company found. Run CompanySeeder first.');
            return;
        }
        $companyId = $company->id;

        $admin = UserModel::where('email', 'admin@empresademo.com')->first();

        // ── Employee Profiles ─────────────────────────────────────────
        $profilesData = [
            ['name' => 'Ana Silva', 'nif' => '123456789', 'phone' => '+351 911 111 111', 'position' => 'Desenvolvedor', 'document_type' => 'BI', 'document_number' => '12345678'],
            ['name' => 'Carlos Pereira', 'nif' => '987654321', 'phone' => '+351 922 222 222', 'position' => 'Gestor de TI', 'document_type' => 'BI', 'document_number' => '87654321'],
            ['name' => 'Mariana Costa', 'nif' => '456789123', 'phone' => '+351 933 333 333', 'position' => 'Recursos Humanos', 'document_type' => 'AR', 'document_number' => 'AR987654'],
            ['name' => 'João Santos', 'nif' => '321654987', 'phone' => '+351 944 444 444', 'position' => 'Técnico de Informática', 'document_type' => 'BI', 'document_number' => '45678912'],
            ['name' => 'Sofia Martins', 'nif' => '654321789', 'phone' => '+351 955 555 555', 'position' => 'Financeiro', 'document_type' => 'BI', 'document_number' => '78912345'],
        ];

        $profileIds = [];
        foreach ($profilesData as $data) {
            $profile = EmployeeProfileModel::updateOrCreate(
                ['name' => $data['name'], 'company_id' => $companyId],
                array_merge($data, [
                    'company_id' => $companyId,
                    'status' => 'active',
                ])
            );
            $profileIds[] = $profile->id;
        }
        $this->command->info('Created ' . count($profilesData) . ' employee profiles.');

        // ── Extra Users ───────────────────────────────────────────────
        $usersData = [
            ['name' => 'Ana Silva', 'email' => 'ana@empresademo.com', 'profile_idx' => 0],
            ['name' => 'Carlos Pereira', 'email' => 'carlos@empresademo.com', 'profile_idx' => 1],
            ['name' => 'Mariana Costa', 'email' => 'mariana@empresademo.com', 'profile_idx' => 2],
        ];

        $userIds = [$admin->id];
        foreach ($usersData as $data) {
            $user = UserModel::where('email', $data['email'])->first();
            if (!$user) {
                $user = UserModel::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make('123'),
                    'company_id' => $companyId,
                    'employee_profile_id' => $profileIds[$data['profile_idx']],
                    'department_id' => DepartmentModel::where('company_id', $companyId)->inRandomOrder()->first()->id,
                    'status' => 'active',
                    'hire_date' => now()->subDays(rand(30, 365))->format('Y-m-d'),
                ]);
            }
            $userIds[] = $user->id;
        }
        $this->command->info('Created ' . count($usersData) . ' additional users.');

        // ── Equipment ─────────────────────────────────────────────────
        $types = EquipmentTypeModel::where('company_id', $companyId)->pluck('id', 'name');

        $equipmentData = [
            ['type' => 'Portátil', 'brand' => 'Dell', 'model' => 'Latitude 5540', 'serial' => 'DL-5540-001', 'supplier' => 'Dell Portugal', 'notes' => 'Core i7, 16GB RAM, 512GB SSD'],
            ['type' => 'Portátil', 'brand' => 'Lenovo', 'model' => 'ThinkPad X1', 'serial' => 'LN-X1-002', 'supplier' => 'Lenovo PT', 'notes' => 'Core i5, 16GB RAM, 256GB SSD'],
            ['type' => 'Computador', 'brand' => 'HP', 'model' => 'EliteDesk 800', 'serial' => 'HP-ED800-003', 'supplier' => 'HP Portugal', 'notes' => 'Core i7, 32GB RAM, 1TB SSD'],
            ['type' => 'Telemóvel', 'brand' => 'Samsung', 'model' => 'Galaxy S24', 'serial' => 'SS-GS24-004', 'supplier' => 'Samsung PT'],
            ['type' => 'Telemóvel', 'brand' => 'Apple', 'model' => 'iPhone 15 Pro', 'serial' => 'AP-IP15-005', 'supplier' => 'Apple Portugal'],
            ['type' => 'Monitor', 'brand' => 'Dell', 'model' => 'UltraSharp 27"', 'serial' => 'DL-US27-006', 'supplier' => 'Dell Portugal'],
            ['type' => 'Tablet', 'brand' => 'Apple', 'model' => 'iPad Air M2', 'serial' => 'AP-IPA-007', 'supplier' => 'Apple Portugal'],
            ['type' => 'Impressora', 'brand' => 'HP', 'model' => 'LaserJet Pro', 'serial' => 'HP-LJ-008', 'supplier' => 'HP Portugal', 'status' => 'maintenance', 'notes' => 'Com aviso: necesita manutenção preventiva'],
            ['type' => 'Licença', 'brand' => 'Microsoft', 'model' => 'Office 365 Business', 'serial' => 'MS-O365-009', 'supplier' => 'Microsoft'],
            ['type' => 'Scanner', 'brand' => 'Epson', 'model' => 'WorkForce DS-530', 'serial' => 'EP-WF530-010', 'supplier' => 'Epson PT'],
        ];

        $equipmentIds = [];
        foreach ($equipmentData as $i => $data) {
            $eq = EquipmentModel::updateOrCreate(
                ['serial_number' => $data['serial']],
                [
                    'company_id' => $companyId,
                    'equipment_type_id' => $types[$data['type']] ?? $types->first(),
                    'serial_number' => $data['serial'],
                    'brand' => $data['brand'],
                    'model' => $data['model'],
                    'status' => $data['status'] ?? ($i < 5 ? 'assigned' : 'available'),
                    'supplier' => $data['supplier'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'purchase_date' => now()->subDays(rand(60, 730))->format('Y-m-d'),
                ]
            );
            $equipmentIds[] = $eq->id;
        }
        $this->command->info('Created ' . count($equipmentData) . ' equipment items.');

        // ── Assignments ───────────────────────────────────────────────
        $assignmentsData = [
            ['equipment_idx' => 0, 'user_idx' => 1],
            ['equipment_idx' => 1, 'user_idx' => 0],
            ['equipment_idx' => 2, 'user_idx' => 2],
            ['equipment_idx' => 3, 'user_idx' => 0],
            ['equipment_idx' => 4, 'user_idx' => 1],
        ];

        $created = 0;
        foreach ($assignmentsData as $data) {
            $eqId = $equipmentIds[$data['equipment_idx']];
            $uId = $userIds[$data['user_idx']];
            $existing = EquipmentAssignmentModel::where('equipment_id', $eqId)
                ->where('status', 'active')->first();
            if ($existing) continue;

            $assignedAt = now()->subDays(rand(1, 60));
            \Illuminate\Support\Facades\DB::statement(
                "INSERT INTO equipment_assignments (equipment_id, user_id, assigned_by, assigned_at, status, notes, created_at, updated_at)
                 VALUES (?, ?, ?, CONVERT(datetime, ?, 20), ?, ?, GETDATE(), GETDATE())",
                [
                    $eqId,
                    $uId,
                    $admin->id,
                    $assignedAt->format('Y-m-d H:i:s'),
                    'active',
                    'Atribuição de teste',
                ]
            );
            $created++;
        }
        $this->command->info("Created {$created} equipment assignments.");
        $this->command->info('');
        $this->command->info('=== Test data created successfully! ===');
        $this->command->info('Login: admin@empresademo.com / 123');
    }
}
