<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * This will set up the complete application with:
     * - Roles and Permissions
     * - Organization Structure
     * - PUM System (Roles, Users, Workflow)
     * - Admin User
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('  Database Seeding Started');
        $this->command->info('========================================');
        $this->command->line('');

        // Step 1: Roles and Permissions
        $this->command->info('Step 1: Setting up Roles and Permissions...');
        $this->call(RolePermissionSeeder::class);
        $this->command->line('');

        // Step 2: Organization Structure
        $this->command->info('Step 2: Setting up Organization Structure...');
        $this->call([
            OrganizationTypeSeeder::class,
            OrganizationUnitSeeder::class,
        ]);
        $this->command->line('');

        // Step 3: PUM Permissions
        $this->command->info('Step 3: Adding PUM Permissions...');
        $this->call([
            AddPumPermissionsSeeder::class,
            AssignDashboardPermissionSeeder::class,
            AssignPumPermissionsToAllRolesSeeder::class,
        ]);
        $this->command->line('');

        // Step 4: PUM Complete Setup
        $this->command->info('Step 4: Setting up PUM System...');
        $this->call(PumCompleteSeeder::class);
        $this->command->line('');

        // Step 5: Create Admin User
        $this->command->info('Step 5: Creating Admin User...');
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => \Hash::make('password'),
                'role_id' => $adminRole?->id,
            ]
        );

        $this->command->info('✓ Admin user created: admin@example.com');
        $this->command->line('');

        $this->command->info('========================================');
        $this->command->info('  Database Seeding Complete!');
        $this->command->info('========================================');
        $this->command->line('');
        
        $this->command->info('Admin Login:');
        $this->command->line('  Email: admin@example.com');
        $this->command->line('  Password: password');
        $this->command->line('');
        
        $this->command->info('PUM Test Users:');
        $this->command->line('  • manager@pum.test (Manager)');
        $this->command->line('  • keuangan@pum.test (Keuangan)');
        $this->command->line('  • direktur@pum.test (Direktur)');
        $this->command->line('  • staff@pum.test (Staff)');
        $this->command->line('  Password: password');
        $this->command->line('');
    }
}
