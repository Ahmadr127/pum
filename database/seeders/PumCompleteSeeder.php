<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PumCompleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder will set up complete PUM system:
     * 1. Roles (Manager, Keuangan, Direktur, Staff)
     * 2. Users (one for each role)
     * 3. Workflow (Default 3-level approval)
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('  Setting up PUM System');
        $this->command->info('========================================');
        $this->command->line('');

        // Step 1: Create Roles
        $this->command->info('Step 1: Creating Roles...');
        $this->call(PumRoleSeeder::class);
        $this->command->line('');

        // Step 2: Create Users
        $this->command->info('Step 2: Creating Users...');
        $this->call(PumUserSeeder::class);
        $this->command->line('');

        // Step 3: Create Workflow
        $this->command->info('Step 3: Creating Approval Workflow...');
        $this->call(PumWorkflowSeeder::class);
        $this->command->line('');

        $this->command->info('========================================');
        $this->command->info('  PUM System Setup Complete!');
        $this->command->info('========================================');
        $this->command->line('');
        $this->command->info('Test Users Created:');
        $this->command->line('  • manager@pum.test (Manager)');
        $this->command->line('  • keuangan@pum.test (Keuangan)');
        $this->command->line('  • direktur@pum.test (Direktur)');
        $this->command->line('  • staff@pum.test (Staff)');
        $this->command->line('');
        $this->command->info('Default Password: password');
        $this->command->line('');
    }
}
