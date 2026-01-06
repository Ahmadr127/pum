<?php

namespace Database\Seeders;

use App\Models\PumApprovalWorkflow;
use App\Models\PumApprovalStep;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PumWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $managerRole = Role::where('name', 'manager')->first();
        $keuanganRole = Role::where('name', 'keuangan')->first();
        $direkturRole = Role::where('name', 'direktur')->first();

        if (!$managerRole || !$keuanganRole || !$direkturRole) {
            $this->command->error('Required roles not found. Please run PumRoleSeeder first.');
            return;
        }

        // Create default workflow
        $workflow = PumApprovalWorkflow::firstOrCreate(
            ['name' => 'Default Approval Workflow'],
            [
                'description' => 'Workflow persetujuan uang muka standar dengan 3 tingkat approval: Manager Departemen → Keuangan → Direktur',
                'is_active' => true,
                'is_default' => true,
            ]
        );

        // Delete old approval steps for this workflow
        PumApprovalStep::where('workflow_id', $workflow->id)->delete();

        // Create approval steps
        $steps = [
            [
                'order' => 1,
                'name' => 'Approval Manager',
                'approver_type' => 'role',
                'role_id' => $managerRole->id,
                'user_id' => null,
                'is_required' => true,
            ],
            [
                'order' => 2,
                'name' => 'Approval Keuangan',
                'approver_type' => 'role',
                'role_id' => $keuanganRole->id,
                'user_id' => null,
                'is_required' => true,
            ],
            [
                'order' => 3,
                'name' => 'Approval Direktur',
                'approver_type' => 'role',
                'role_id' => $direkturRole->id,
                'user_id' => null,
                'is_required' => true,
            ],
        ];

        foreach ($steps as $stepData) {
            PumApprovalStep::create(array_merge(
                ['workflow_id' => $workflow->id],
                $stepData
            ));
        }

        $this->command->info('✓ Default PUM workflow created with ' . count($steps) . ' approval steps.');
        $this->command->info('✓ Workflow: Manager → Keuangan → Direktur');
        $this->command->line('');
        $this->command->info('Approval Steps:');
        foreach ($steps as $step) {
            $this->command->line("  {$step['order']}. {$step['name']}");
        }
    }
}
