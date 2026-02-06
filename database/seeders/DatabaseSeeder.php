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
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@azra.com',
                'password' => \Hash::make('password'),
                'role_id' => $adminRole?->id,
            ]
        );

        $this->command->info('✓ Admin user processed: ' . $admin->email);
        $this->command->line('');

        $this->command->info('========================================');
        $this->command->info('  Database Seeding Complete!');
        $this->command->info('========================================');
        $this->command->line('');
        
        $this->command->info('Admin Login:');
        $this->command->line('  Email: admin@azra.com');
        $this->command->line('  Password: password');
        $this->command->line('');
        
        $this->command->info('PUM Test Users:');
        $this->command->line('  • direktur.utama (Direktur) - Dr. Ahmad Direktur');
        $this->command->line('  Password: password');
        $this->command->line('');
        $this->command->info('Note:');
        $this->command->line('  • User Manager PUM dan Staff PUM dihapus (tidak digunakan)');
        $this->command->line('  • User Keuangan: siti.keuangan (Departemen Keuangan)');
        $this->command->line('');
    }
}
