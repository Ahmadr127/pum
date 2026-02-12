<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PumApprovalWorkflow;
use App\Models\PumApprovalStep;
use App\Models\PumWorkflowCondition;
use App\Models\Role;

class PumWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing workflows
        PumApprovalWorkflow::query()->delete();

        $this->command->info('Creating Workflows based on Design...');

        // ==========================================
        // BARANG BARU
        // ==========================================

        // 1. Barang Baru < 10 Juta
        $this->createWorkflow(
            'Barang Baru - < 10 Juta',
            'barang_baru',
            null,
            10000000,
            [
                ['name' => 'Manager Unit', 'type' => 'approval', 'approver_type' => 'organization_head'],
                ['name' => 'Hospital Director', 'type' => 'approval', 'role' => 'hospital_director'],
                ['name' => 'Manager PT', 'type' => 'approval', 'role' => 'manager_pt'],
                ['name' => 'Manager Pembelian', 'type' => 'approval', 'role' => 'manajer_pembelian'],

            ]
        );

        // 2. Barang Baru 10 - 50 Juta
        $this->createWorkflow(
            'Barang Baru - 10 s/d 50 Juta',
            'barang_baru',
            10000000,
            50000000,
            [
                ['name' => 'Manager Unit', 'type' => 'approval', 'approver_type' => 'organization_head'],
                ['name' => 'Hospital Director', 'type' => 'approval', 'role' => 'hospital_director'],
                ['name' => 'Manager PT', 'type' => 'approval', 'role' => 'manager_pt'],
                ['name' => 'Manager Pembelian', 'type' => 'approval', 'role' => 'manajer_pembelian'],

            ]
        );

        // 3. Barang Baru > 50 Juta
        $this->createWorkflow(
            'Barang Baru - > 50 Juta',
            'barang_baru',
            50000000,
            null,
            [
                ['name' => 'Manager Unit', 'type' => 'approval', 'approver_type' => 'organization_head'],
                ['name' => 'Manager Keuangan', 'type' => 'approval', 'role' => 'manajer_keuangan', 'fs' => true], // FS Required
                ['name' => 'Hospital Director', 'type' => 'approval', 'role' => 'hospital_director'],
                ['name' => 'Manager PT', 'type' => 'approval', 'role' => 'manager_pt'],
                ['name' => 'Manager Pembelian', 'type' => 'approval', 'role' => 'manajer_pembelian'],

            ]
        );

        // ==========================================
        // PEREMAJAAN
        // ==========================================

        // 4. Peremajaan < 10 Juta
        $this->createWorkflow(
            'Peremajaan - < 10 Juta',
            'peremajaan',
            null,
            10000000,
            [
                ['name' => 'Manager Unit', 'type' => 'approval', 'approver_type' => 'organization_head'],
                ['name' => 'Hospital Director', 'type' => 'approval', 'role' => 'hospital_director'],
                ['name' => 'Manager Pembelian', 'type' => 'approval', 'role' => 'manajer_pembelian'],

            ]
        );

        // 5. Peremajaan 10 - 50 Juta
        $this->createWorkflow(
            'Peremajaan - 10 s/d 50 Juta',
            'peremajaan',
            10000000,
            50000000,
            [
                ['name' => 'Manager Unit', 'type' => 'approval', 'approver_type' => 'organization_head'],
                ['name' => 'Hospital Director', 'type' => 'approval', 'role' => 'hospital_director'],
                ['name' => 'Manager PT', 'type' => 'approval', 'role' => 'manager_pt'],
                ['name' => 'Manager Pembelian', 'type' => 'approval', 'role' => 'manajer_pembelian'],

            ]
        );

        // 6. Peremajaan > 50 Juta
        $this->createWorkflow(
            'Peremajaan - > 50 Juta',
            'peremajaan',
            50000000,
            null,
            [
                ['name' => 'Manager Unit', 'type' => 'approval', 'approver_type' => 'organization_head'],
                ['name' => 'Manager Keuangan', 'type' => 'approval', 'role' => 'manajer_keuangan', 'fs' => true], // FS Required
                ['name' => 'Hospital Director', 'type' => 'approval', 'role' => 'hospital_director'],
                ['name' => 'Manager PT', 'type' => 'approval', 'role' => 'manager_pt'],
                ['name' => 'Manager Pembelian', 'type' => 'approval', 'role' => 'manajer_pembelian'],

            ]
        );

        $this->command->info('All workflows created successfully!');
    }

    /**
     * Create a workflow with conditions and steps
     */
    private function createWorkflow($name, $category, $min, $max, $steps)
    {
        $workflow = PumApprovalWorkflow::create([
            'name' => $name,
            'description' => "Workflow {$name}",
            'is_active' => true,
            'is_default' => false,
        ]);

        PumWorkflowCondition::create([
            'workflow_id' => $workflow->id,
            'procurement_category' => $category,
            'amount_min' => $min,
            'amount_max' => $max,
            'priority' => 1,
        ]);

        foreach ($steps as $index => $step) {
            $roleId = null;
            if (isset($step['role'])) {
                $role = Role::where('name', $step['role'])->first();
                $roleId = $role ? $role->id : null;
            }

            PumApprovalStep::create([
                'workflow_id' => $workflow->id,
                'order' => $index + 1,
                'name' => $step['name'],
                'type' => $step['type'],
                'approver_type' => $step['approver_type'] ?? 'role',
                'role_id' => $roleId,
                'is_required' => true,
                'is_upload_fs_required' => $step['fs'] ?? false,
            ]);
        }
    }
}
