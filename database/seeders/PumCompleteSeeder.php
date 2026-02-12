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
     * 2. Users (Direktur only)
     * 3. Workflow (Default 3-level approval)
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('  Setting up PUM System');
        $this->command->info('========================================');
        $this->command->line('');

        // Step 1: Roles already created in DatabaseSeeder
        $this->command->info('Step 1: Roles verified.');

        // Step 2: Create Users
        $this->command->info('Step 2: Setting up Users...');
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
        $this->command->line('  • irma.rismayanti (Hospital Director)');
        $this->command->line('  • manager.pt (Manager PT)');
        $this->command->line('  • direktur.pt (Direktur PT)');
        $this->command->line('');
        $this->command->info('Default Password: password');
        $this->command->info('Note:');
        $this->command->line('  • Roles updated to match approval workflow.');
        $this->command->line('');
    }
}
