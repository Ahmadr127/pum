<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            ['name' => 'manage_roles', 'display_name' => 'Kelola Roles', 'description' => 'Mengelola roles dan permissions'],
            ['name' => 'manage_permissions', 'display_name' => 'Kelola Permissions', 'description' => 'Mengelola permissions'],
            ['name' => 'view_dashboard', 'display_name' => 'Lihat Dashboard', 'description' => 'Melihat halaman dashboard'],
            ['name' => 'manage_users', 'display_name' => 'Kelola Users', 'description' => 'Mengelola pengguna'],
            ['name' => 'manage_organization_types', 'display_name' => 'Kelola Tipe Organisasi', 'description' => 'Mengelola tipe organisasi'],
            ['name' => 'manage_organization_units', 'display_name' => 'Kelola Unit Organisasi', 'description' => 'Mengelola unit organisasi'],
            ['name' => 'manage_pum', 'display_name' => 'Kelola Permintaan Uang Muka', 'description' => 'Membuat dan mengelola permintaan uang muka'],
            ['name' => 'manage_pum_workflows', 'display_name' => 'Kelola Workflow PUM', 'description' => 'Mengelola workflow approval permintaan uang muka'],
            ['name' => 'approve_pum', 'display_name' => 'Approval Uang Muka', 'description' => 'Menyetujui atau menolak permintaan uang muka'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Create Roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Role dengan akses penuh ke sistem'
            ]
        );

        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Manager',
                'description' => 'Manager unit organisasi dengan hak approval'
            ]
        );

        $direkturRole = Role::firstOrCreate(
            ['name' => 'hospital_director'],
            [
                'display_name' => 'Hospital Director',
                'description' => 'Hospital Director'
            ]
        );

        $managerPtRole = Role::firstOrCreate(
            ['name' => 'manager_pt'],
            [
                'display_name' => 'Manager PT',
                'description' => 'Manager PT'
            ]
        );

        $direkturPtRole = Role::firstOrCreate(
            ['name' => 'direktur_pt'],
            [
                'display_name' => 'Direktur PT',
                'description' => 'Direktur PT'
            ]
        );

        $keuanganRole = Role::firstOrCreate(
            ['name' => 'keuangan'],
            [
                'display_name' => 'Keuangan',
                'description' => 'Bagian Keuangan dengan hak approval'
            ]
        );

        // Assign permissions to roles
        $adminRole->permissions()->attach(Permission::all()); // Admin gets all permissions
        
        // Manager gets specific permissions
        $managerRole->permissions()->attach(
            Permission::whereIn('name', [
                'view_dashboard',
                'manage_pum',
                'approve_pum'
            ])->get()
        );

        // Direktur gets specific permissions
        $direkturRole->permissions()->sync(
            Permission::whereIn('name', [
                'view_dashboard',
                'approve_pum'
            ])->get()
        );

        $managerPtRole->permissions()->sync(
            Permission::whereIn('name', [
                'view_dashboard',
                'approve_pum'
            ])->get()
        );

        $direkturPtRole->permissions()->sync(
             Permission::whereIn('name', [
                 'view_dashboard',
                 'approve_pum'
             ])->get()
         );

        // Keuangan gets specific permissions
        $keuanganRole->permissions()->attach(
            Permission::whereIn('name', [
                'view_dashboard',
                'approve_pum'
            ])->get()
        );
    }
}
