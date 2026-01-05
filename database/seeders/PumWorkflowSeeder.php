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
        // Create default workflow
        $workflow = PumApprovalWorkflow::firstOrCreate(
            ['name' => 'Default Approval Workflow'],
            [
                'description' => 'Workflow persetujuan uang muka standar: Manager → Keuangan → Direktur',
                'is_active' => true,
                'is_default' => true,
            ]
        );

        // Get or create roles
        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            ['display_name' => 'Manager', 'description' => 'Manager dengan hak approval']
        );

        $financeRole = Role::firstOrCreate(
            ['name' => 'keuangan'],
            ['display_name' => 'Keuangan', 'description' => 'Bagian Keuangan']
        );

        $directorRole = Role::firstOrCreate(
            ['name' => 'direktur'],
            ['display_name' => 'Direktur', 'description' => 'Direktur Perusahaan']
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
                'is_required' => true,
            ],
            [
                'order' => 2,
                'name' => 'Approval Keuangan',
                'approver_type' => 'role',
                'role_id' => $financeRole->id,
                'is_required' => true,
            ],
            [
                'order' => 3,
                'name' => 'Approval Direktur',
                'approver_type' => 'role',
                'role_id' => $directorRole->id,
                'is_required' => true,
            ],
        ];

        foreach ($steps as $stepData) {
            PumApprovalStep::create(array_merge(
                ['workflow_id' => $workflow->id],
                $stepData
            ));
        }

        $this->command->info('Default PUM workflow created with ' . count($steps) . ' approval steps.');
        $this->command->info('Workflow: Manager → Keuangan → Direktur');
    }
}
