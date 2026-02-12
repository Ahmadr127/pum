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

        // Create create_pum permission if it doesn't exist
        Permission::firstOrCreate(
            ['name' => 'create_pum'],
            ['display_name' => 'Buat PUM', 'description' => 'Membuat dan melihat pengajuan PUM sendiri']
        );

        // Define role-specific permissions
        // Note: All roles except directors (hospital_director, direktur_pt) can create PUM
        $rolePermissions = [
            'admin' => ['manage_pum', 'manage_pum_workflows', 'approve_pum', 'create_pum'],
            'hospital_director' => ['approve_pum'], // Director only approves, doesn't create
            'manager_pt' => ['approve_pum', 'create_pum'], // Manager PT can approve and create
            'direktur_pt' => ['approve_pum'], // Direktur PT only approves, doesn't create
            'manajer_keuangan' => ['approve_pum', 'create_pum'], // Can approve and create
            'manajer_pembelian' => ['approve_pum', 'create_pum'], // Can approve and create
            'manager' => ['approve_pum', 'create_pum'], // Generic manager can approve and create
            'staff' => ['create_pum'], // Staff can only create PUM requests
            'keuangan' => ['approve_pum', 'create_pum'], // Keuangan can approve and create
        ];

        foreach ($rolePermissions as $roleName => $permNames) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                // First, remove all PUM permissions
                $allPumPerms = Permission::whereIn('name', ['manage_pum', 'manage_pum_workflows', 'approve_pum', 'create_pum'])->pluck('id');
                $role->permissions()->detach($allPumPerms);
                
                // Then assign the correct ones
                if (!empty($permNames)) {
                    $perms = Permission::whereIn('name', $permNames)->pluck('id');
                    $role->permissions()->attach($perms);
                    echo "Role '{$roleName}': " . implode(', ', $permNames) . "\n";
                } else {
                    echo "Role '{$roleName}': (no PUM permissions)\n";
                }
            }
        }
        
        echo "\nDone! Permissions assigned based on role.\n";
    }
}
