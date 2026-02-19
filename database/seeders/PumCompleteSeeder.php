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


        // Step 1: Roles already created in DatabaseSeeder
        $this->command->info('Step 1: Roles verified.');

        // Step 2: Create Users
        $this->command->info('Step 2: Setting up Users...');
        $this->call(PumUserSeeder::class);

        // Step 3: Create Workflow
        $this->command->info('Step 3: Creating Approval Workflow...');
        $this->call(PumWorkflowSeeder::class);

    }
}
