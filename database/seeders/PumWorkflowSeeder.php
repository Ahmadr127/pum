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

        // Get roles (assuming they exist from PumRoleSeeder)
        $roles = [
            'manager' => Role::where('name', 'manager')->first(),
            'koordinator' => Role::where('name', 'koordinator')->first(),
            'supervisor' => Role::where('name', 'supervisor')->first(),
            'kepala_unit' => Role::where('name', 'kepala_unit')->first(),
            'manajer_pembelian' => Role::where('name', 'manajer_pembelian')->first(),
            'direktur_operasional' => Role::where('name', 'direktur_operasional')->first(),
            'spv_1' => Role::where('name', 'spv_1')->first(),
            'manajer_keuangan' => Role::where('name', 'manajer_keuangan')->first(),
            'direktur_it' => Role::where('name', 'direktur_it')->first(),
            'manajer_it' => Role::where('name', 'manajer_it')->first(),
            'direktur_keuangan' => Role::where('name', 'direktur_keuangan')->first(),
            'direktur_utama' => Role::where('name', 'direktur_utama')->first(),
        ];

        // 1. Default Workflow (Manager Pengaju only)
        $this->command->info('Creating default workflow...');
        $defaultWorkflow = PumApprovalWorkflow::create([
            'name' => 'Default - Manager Pengaju',
            'description' => 'Workflow default untuk approval Manager Pengaju',
            'is_active' => true,
            'is_default' => true,
        ]);

        PumApprovalStep::create([
            'workflow_id' => $defaultWorkflow->id,
            'order' => 1,
            'name' => 'Approval Manager Pengaju',
            'approver_type' => 'organization_head',
            'is_required' => true,
        ]);

        // 2. Barang Baru - < 10 Juta
        $this->command->info('Creating Barang Baru < 10M workflow...');
        $this->createWorkflow(
            'Barang Baru - Kurang dari 10 Juta',
            'barang_baru',
            null,
            10000000,
            [
                ['name' => 'Manager Pengaju', 'type' => 'organization_head'],
                ['name' => 'Koordinator', 'role' => $roles['koordinator']],
                ['name' => 'Supervisor', 'role' => $roles['supervisor']],
                ['name' => 'Kepala Unit', 'role' => $roles['kepala_unit']],
                ['name' => 'Manajer Pembelian', 'role' => $roles['manajer_pembelian']],
                ['name' => 'Direktur Operasional', 'role' => $roles['direktur_operasional']],
                ['name' => 'SPV 1', 'role' => $roles['spv_1']],
            ]
        );

        // 3. Barang Baru - 10-50 Juta
        $this->command->info('Creating Barang Baru 10-50M workflow...');
        $this->createWorkflow(
            'Barang Baru - 10 sampai 50 Juta',
            'barang_baru',
            10000000,
            50000000,
            [
                ['name' => 'Manager Pengaju', 'type' => 'organization_head'],
                ['name' => 'Koordinator', 'role' => $roles['koordinator']],
                ['name' => 'Supervisor', 'role' => $roles['supervisor']],
                ['name' => 'Kepala Unit', 'role' => $roles['kepala_unit']],
                ['name' => 'Manajer Pembelian', 'role' => $roles['manajer_pembelian']],
                ['name' => 'Direktur Operasional', 'role' => $roles['direktur_operasional']],
                ['name' => 'SPV 1', 'role' => $roles['spv_1']],
                ['name' => 'Manajer Keuangan', 'role' => $roles['manajer_keuangan']],
                ['name' => 'Direktur IT', 'role' => $roles['direktur_it']],
            ]
        );

        // 4. Barang Baru - > 50 Juta
        $this->command->info('Creating Barang Baru > 50M workflow...');
        $this->createWorkflow(
            'Barang Baru - Lebih dari 50 Juta',
            'barang_baru',
            50000000,
            null,
            [
                ['name' => 'Manager Pengaju', 'type' => 'organization_head'],
                ['name' => 'Koordinator', 'role' => $roles['koordinator']],
                ['name' => 'Supervisor', 'role' => $roles['supervisor']],
                ['name' => 'Kepala Unit', 'role' => $roles['kepala_unit']],
                ['name' => 'Manajer Pembelian', 'role' => $roles['manajer_pembelian']],
                ['name' => 'Direktur Operasional', 'role' => $roles['direktur_operasional']],
                ['name' => 'SPV 1', 'role' => $roles['spv_1']],
                ['name' => 'Manajer Keuangan', 'role' => $roles['manajer_keuangan']],
                ['name' => 'Direktur IT', 'role' => $roles['direktur_it']],
                ['name' => 'Manajer IT', 'role' => $roles['manajer_it']],
                ['name' => 'Direktur Keuangan', 'role' => $roles['direktur_keuangan']],
                ['name' => 'Direktur IT (2)', 'role' => $roles['direktur_it']],
                ['name' => 'Direktur Utama', 'role' => $roles['direktur_utama']],
            ]
        );

        // 5. Peremajaan - < 10 Juta
        $this->command->info('Creating Peremajaan < 10M workflow...');
        $this->createWorkflow(
            'Peremajaan - Kurang dari 10 Juta',
            'peremajaan',
            null,
            10000000,
            [
                ['name' => 'Manager Pengaju', 'type' => 'organization_head'],
                ['name' => 'Koordinator', 'role' => $roles['koordinator']],
                ['name' => 'Supervisor', 'role' => $roles['supervisor']],
                ['name' => 'Kepala Unit', 'role' => $roles['kepala_unit']],
                ['name' => 'Manajer Pembelian', 'role' => $roles['manajer_pembelian']],
                ['name' => 'Direktur Operasional', 'role' => $roles['direktur_operasional']],
                ['name' => 'SPV 1', 'role' => $roles['spv_1']],
            ]
        );

        // 6. Peremajaan - 10-50 Juta
        $this->command->info('Creating Peremajaan 10-50M workflow...');
        $this->createWorkflow(
            'Peremajaan - 10 sampai 50 Juta',
            'peremajaan',
            10000000,
            50000000,
            [
                ['name' => 'Manager Pengaju', 'type' => 'organization_head'],
                ['name' => 'Koordinator', 'role' => $roles['koordinator']],
                ['name' => 'Supervisor', 'role' => $roles['supervisor']],
                ['name' => 'Kepala Unit', 'role' => $roles['kepala_unit']],
                ['name' => 'Manajer Pembelian', 'role' => $roles['manajer_pembelian']],
                ['name' => 'Direktur Operasional', 'role' => $roles['direktur_operasional']],
                ['name' => 'SPV 1', 'role' => $roles['spv_1']],
                ['name' => 'Manajer Keuangan', 'role' => $roles['manajer_keuangan']],
                ['name' => 'Direktur IT', 'role' => $roles['direktur_it']],
            ]
        );

        // 7. Peremajaan - > 50 Juta
        $this->command->info('Creating Peremajaan > 50M workflow...');
        $this->createWorkflow(
            'Peremajaan - Lebih dari 50 Juta',
            'peremajaan',
            50000000,
            null,
            [
                ['name' => 'Manager Pengaju', 'type' => 'organization_head'],
                ['name' => 'Koordinator', 'role' => $roles['koordinator']],
                ['name' => 'Supervisor', 'role' => $roles['supervisor']],
                ['name' => 'Kepala Unit', 'role' => $roles['kepala_unit']],
                ['name' => 'Manajer Pembelian', 'role' => $roles['manajer_pembelian']],
                ['name' => 'Direktur Operasional', 'role' => $roles['direktur_operasional']],
                ['name' => 'SPV 1', 'role' => $roles['spv_1']],
                ['name' => 'Manajer Keuangan', 'role' => $roles['manajer_keuangan']],
                ['name' => 'Direktur IT', 'role' => $roles['direktur_it']],
                ['name' => 'Manajer IT', 'role' => $roles['manajer_it']],
                ['name' => 'Direktur Keuangan', 'role' => $roles['direktur_keuangan']],
                ['name' => 'Direktur IT (2)', 'role' => $roles['direktur_it']],
                ['name' => 'Direktur Utama', 'role' => $roles['direktur_utama']],
            ]
        );

        $this->command->info('All workflows created successfully!');
    }

    /**
     * Create a workflow with conditions and steps
     */
    private function createWorkflow($name, $category, $amountMin, $amountMax, $steps)
    {
        $workflow = PumApprovalWorkflow::create([
            'name' => $name,
            'description' => "Workflow untuk {$name}",
            'is_active' => true,
            'is_default' => false,
        ]);

        // Create condition
        PumWorkflowCondition::create([
            'workflow_id' => $workflow->id,
            'procurement_category' => $category,
            'amount_min' => $amountMin,
            'amount_max' => $amountMax,
            'priority' => 1,
        ]);

        // Create steps
        foreach ($steps as $index => $stepData) {
            PumApprovalStep::create([
                'workflow_id' => $workflow->id,
                'order' => $index + 1,
                'name' => $stepData['name'],
                'approver_type' => $stepData['type'] ?? 'role',
                'role_id' => isset($stepData['role']) ? $stepData['role']->id : null,
                'is_required' => true,
            ]);
        }
    }
}
