<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class AssignPumPermissionsToAllRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Create PUM permissions if they don't exist
        $permissions = [
            ['name' => 'manage_pum', 'display_name' => 'Kelola Permintaan Uang Muka', 'description' => 'Membuat dan mengelola permintaan uang muka'],
            ['name' => 'manage_pum_workflows', 'display_name' => 'Kelola Workflow PUM', 'description' => 'Mengelola workflow approval permintaan uang muka'],
            ['name' => 'approve_pum', 'display_name' => 'Approval Uang Muka', 'description' => 'Menyetujui atau menolak permintaan uang muka'],
        ];

        foreach ($permissions as $permData) {
            Permission::firstOrCreate(
                ['name' => $permData['name']],
                $permData
            );
        }

        // Define role-specific permissions
        $rolePermissions = [
            'admin' => ['manage_pum', 'manage_pum_workflows', 'approve_pum'], // All permissions
            'manager' => ['approve_pum'], // Only approve
            'director' => ['approve_pum'], // Only approve
            'finance' => ['approve_pum', 'manage_pum'], // Approve and manage requests
            'staff' => [], // No management permissions, only create (handled by PumPegawaiPermissionSeeder)
            'user' => [], // No management permissions
        ];

        foreach ($rolePermissions as $roleName => $permNames) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                // First, remove all PUM permissions
                $allPumPerms = Permission::whereIn('name', ['manage_pum', 'manage_pum_workflows', 'approve_pum'])->pluck('id');
                $role->permissions()->detach($allPumPerms);
                
                // Then assign the correct ones
                $perms = Permission::whereIn('name', $permNames)->pluck('id');
                $role->permissions()->attach($perms);
                echo "Role '{$roleName}': " . implode(', ', $permNames) . "\n";
            }
        }
        
        echo "\nDone! Permissions assigned based on role.\n";
    }
}
