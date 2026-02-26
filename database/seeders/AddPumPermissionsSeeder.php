<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class AddPumPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create new permissions if they don't exist
        $permissions = [
            ['name' => 'manage_pum', 'display_name' => 'Kelola Permintaan Uang Muka', 'description' => 'Membuat dan mengelola permintaan uang muka'],
            ['name' => 'manage_pum_workflows', 'display_name' => 'Kelola Workflow PUM', 'description' => 'Mengelola workflow approval permintaan uang muka'],
            ['name' => 'approve_pum', 'display_name' => 'Approval Uang Muka', 'description' => 'Menyetujui atau menolak permintaan uang muka'],
            ['name' => 'create_pum', 'display_name' => 'Buat PUM', 'description' => 'Membuat dan melihat pengajuan PUM sendiri'],
            ['name' => 'approve_pum_release', 'display_name' => 'Release Uang Muka', 'description' => 'Melakukan release uang muka'],
        ];

        foreach ($permissions as $permData) {
            Permission::firstOrCreate(
                ['name' => $permData['name']],
                $permData
            );
        }

        // Assign all PUM permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $pumPermissions = Permission::whereIn('name', ['manage_pum', 'manage_pum_workflows', 'approve_pum', 'create_pum', 'approve_pum_release'])->get();
            $adminRole->permissions()->syncWithoutDetaching($pumPermissions->pluck('id'));
            echo "PUM permissions assigned to admin role.\n";
        }
    }
}
